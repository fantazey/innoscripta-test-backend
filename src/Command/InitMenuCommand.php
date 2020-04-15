<?php

namespace App\Command;

use App\Entity\{Product, ProductType};
use App\Repository\{ProductRepository, ProductTypeRepository};
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class InitMenuCommand extends Command
{
    protected static $defaultName = 'init-menu';

    /** @var EntityManager $em */
    private $em;
    /** @var ProductRepository $productRepository */
    private $productRepository;
    /** @var ProductTypeRepository $productTypeRepository */
    private $productTypeRepository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $this->em = $em;
        $this->productRepository = $em->getRepository(Product::class);
        $this->productTypeRepository = $em->getRepository(ProductType::class);
    }

    protected function configure()
    {
        $this
            ->setDescription('Command for fill db with base entities')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function initProductTypes()
    {
        $productTypes = ['pizza', 'snack', 'dessert', 'drink', 'souse', 'other'];
        foreach ($productTypes as $name) {
            $type = $this->productTypeRepository->findOneByName($name);
            if (!$type) {
                $type = new ProductType();
                $type->setName($name);
                $this->em->persist($type);
            }
        }
        $this->em->flush();
    }

    /**
     * @param string $type
     * @param array $items
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function initProduct(string $type, array $items)
    {
        $type = $this->productTypeRepository->findOneByName($type);
        if (!$type) {
            return;
        }
        foreach ($items as $item) {
            $product = $this->productRepository->findOneBy(['name' => $item['name'], 'price' => $item['price']]);
            if (!$product) {
                $product = new Product();
                $product->setType($type);
                $product->setName($item['name']);
                $product->setDescription($item['description']);
                $product->setPrice($item['price']);
                $this->em->persist($product);
            }
        }
        $this->em->flush($product);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function initPizzas()
    {
        $items = [
            ['name' => 'Margherita 12"', 'description' => 'Tomato Sauce, Fior di Latte Mozzarella, Fresh Basil', 'price' => 10.5],
            ['name' => 'Calabrian Buffalo Chicken 12"', 'description' => 'Roasted Chicken, Fresh Mozzarella, Gorgonzola, Calabrian Buffalo Sauce, Green Onions', 'price' => 14],
            ['name' => 'Vegan 12"', 'description' => 'Tomato Sauce, House Pesto, Roasted Shrooms, Baby Arugula, Roasted Squash, Caramelized Onion', 'price' => 12.99],
            ['name' => 'Bruce’s Seasonal Special', 'description' => 'Our White Sauce, Fresh Mozzarella, Roasted Chicken, Spinach, Garlic, Roasted Bell Peppers', 'price' => 17],
        ];
        $this->initProduct('pizza', $items);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function initDrinks() {
        $items = [
            ['name' => 'Water 0.5L', 'description' => '', 'price' => 1.49],
            ['name' => 'Cola 0.33L', 'description' => '', 'price' => 2.20],
            ['name' => 'Cappuccino 0.4L', 'description' => '', 'price' => 2]
        ];
        $this->initProduct('drink', $items);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initProductTypes();
        $this->initPizzas();
        $this->initDrinks();
        return 0;
    }
}
