<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Car;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use PDO;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\DBAL\DriverManager;
use DateTime;

#[
    AsCommand(
        name: "carscrapper",
        description: "This is a command for scrapping car data developed by Mouadh El Amri"
    )
]
class CarscrapperCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private $client;

    public function __construct(
        EntityManagerInterface $entityManager,
        HttpClientInterface $client
    ) {
        $this->entityManager = $entityManager;
        $this->client = $client;

        parent::__construct();
    }
    protected function configure(): void
    {
        $this->addArgument(
            "url",
            InputArgument::REQUIRED,
            "The URL of the website to scrape"
        )
            ->addArgument(
                "limit",
                InputArgument::OPTIONAL,
                "The maximum number of cars to scrape",
                150
            )
            ->addOption(
                "reset",
                "r",
                InputOption::VALUE_NONE,
                "Delete all existing cars before scraping"
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        // Establish a database connection and output connection details
        $connection = $this->entityManager->getConnection();
        $params = $connection->getParams();
        $io->writeln(
            sprintf(
                "Database connection: %s@%s:%s/%s",
                $params["user"],
                $params["host"],
                $params["port"],
                $params["dbname"]
            )
        );
        // Get command-line arguments
        $url = $input->getArgument("url");
        $limit = $input->getArgument("limit");
        $reset = $input->getOption("reset");
        $page = 1;
        $carsScraped = 0;
        //If the reset option is set, delete all existing Car entities,you can desactivate it if you want new data persisted with old ones.

        if ($reset) {
            $io->warning("Deleting all existing cars");
            $this->entityManager
                ->createQuery("DELETE FROM App\Entity\Car")
                ->execute();
        }

        $io->writeln(sprintf("Scraping %d cars from %s", $limit, $url));

        try {
            while ($carsScraped < $limit) {

                $pageUrl = $url . "&page=" . $page;
                $io->writeln(sprintf("Scraping page %d: %s", $page, $pageUrl));

                // Scrape page content using the Symfony DomCrawler component
                $crawler = new Crawler(
                    $this->client->request("GET", $url)->getContent()
                );

                // Filter out the list of cars on the page and limit the result to the specified limit
                $carsFilter = ".ListItem_wrapper__J_a_C";
                $cars = $crawler->filter($carsFilter)->slice(0, $limit);
                if ($cars->count() === 0) {
                    break;
                }

                 // Iterate over each car element and extract data
                foreach ($cars as $carElement) {

                    $carCrawler = new Crawler($carElement);

                    $car = new Car();
                    try {
                        //Title
                        $car->setTitle(
                            $carCrawler
                                ->filter(".ListItem_title__znV2I")
                                ->text()
                        );
                        // var_dump($car->getTitle()); // Debug output

                        //RegistrationDate
                    $registrationDate = $carCrawler->filterXPath('//span[@class="VehicleDetailTableNewDesign_item__5LQHk"][3]')->text();
                    $car->setRegistrationDate($registrationDate);
                     // Check if the registration date is 2012 or newer
                    $year = substr($registrationDate, -4);
                    if ($year < 2012) {
                        continue; // skip cars with registration date earlier than 2012
                    }     

                        //Emission
                        $Emission = $carCrawler
                            ->filterXPath(
                                '//span[@class="VehicleDetailTableNewDesign_item__5LQHk"][6]'
                            )
                            ->text();
                            if (empty($Emission)) {
                                continue; // skip cars without emissions data
                            }
                        $car->setEmission($Emission);

                        //price
                        $price = str_replace(
                            ",",
                            ".",
                            $carCrawler->filter(".Price_price__WZayw")->text()
                        );
                        $car->setPrice(
                            (float) preg_replace("/[^\d\.]/", "", $price)
                        );

                        //Mileage
                        $car->setMileage(
                            (float) $carCrawler
                                ->filter(
                                    ".VehicleDetailTableNewDesign_item__5LQHk"
                                )
                                ->text()
                        );

                        //FuelType
                        $FuelType = $carCrawler
                            ->filterXPath(
                                '//span[@class="VehicleDetailTableNewDesign_item__5LQHk"][4]'
                            )
                            ->text();
                        $car->setFuelType($FuelType);

                        //Transmission
                        $car->setTransmission(
                            $carCrawler
                                ->filterXPath(
                                    '//span[@class="VehicleDetailTableNewDesign_item__5LQHk"][2]'
                                )
                                ->text()
                        );

                        //Power
                        $Power = $carCrawler
                            ->filterXPath(
                                '//span[@class="VehicleDetailTableNewDesign_item__5LQHk"][5]'
                            )
                            ->text();
                        $car->setPower($Power);

                        //Equipment
                        $equipment = $carCrawler
                        ->filter(".ListItem_subtitle__eY660")
                        ->text();
                        if (empty($equipment)) {
                        continue; 
                        }
                        $car->setEquipment($equipment);

                        //ExternalID
                        $carsFilter = ".ListPage_main__L0gsf";
                        $carCrawler = $crawler->filter($carsFilter);
                        $address = $carCrawler
                            ->filter(".SellerInfo_address__txoNV")
                            ->text();
                        if (preg_match("/DE-\d+/", $address, $matches)) {
                            $externalId = $matches[0];
                            $car->setExternalId($externalId);
                        }

                        //MainImage
                            $mainImage = $carCrawler->filter('.NewGallery_img__bi92g')->first();
                            $car->setMainImage($mainImage->attr('src'));
                            var_dump($car->getMainImage());  
                
                        
                        //ExteriorColor

                        $carsFilter = ".DetailsSection_container__kJAVE";
                        $carCrawler = $crawler->filter($carsFilter);
                        $carCrawler
                            ->filter("dl > dd")
                            ->each(function ($colorCrawler) use ($car) {
                                $class = $colorCrawler->attr("class");
                                if (
                                    strpos(
                                        $class,
                                        "DataGrid_defaultDdStyle__"
                                    ) !== false
                                ) {
                                    $label = trim(
                                        $colorCrawler
                                            ->previousAll()
                                            ->first()
                                            ->text()
                                    );
                                    $value = trim($colorCrawler->text());
                                    echo "Label: $label | Value: $value\n";
                                    if ($label === "Außenfarbe") {
                                        $car->setExteriorColor($value);
                                    }
                                }
                            });
                        var_dump($car->getExteriorColor()); //debug output

                                    /*
                            // Body Type
                            $carsFilter = '.DetailsSection_childrenSection__NQLD7';
                            $carCrawler = $crawler->filter($carsFilter);
                            $bodyType = $carCrawler->filterXPath('//dt[contains(text(), "Karosserieform")]/following-sibling::dd')->text();
                            $car->setBodyType($bodyType);
                            $io->writeln(sprintf('Body Type: %s', $car->getBodyType()));
                            */

                        $this->entityManager->persist($car);
                        $this->entityManager->flush();
                        $carsScraped++; //Number of carScraped incremented
                    } catch (\Exception $e) {
                        $io->error(
                            sprintf(
                                "Error while scraping car %d: %s",
                                $carsScraped,
                                $e->getMessage()
                            )
                        );
                        continue; // continue to the next car
                    }
                    if ($carsScraped >= $limit) {
                        break 2; // break out of both loops
                    }
                    $io->success(
                        sprintf("Car %d scraped successfully.", $carsScraped)
                    );
                }
                $page++; //the url webpage incremented after being scraped
            }
          //  echo "Number of cars scraped: " . count($cars) . PHP_EOL; // Debug output
            $io->success("Scraping completed successfully.");
        } catch (\Exception $e) {
            $io->error("Error while scraping: " . $e->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}
