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
        $productTypes = ['pizza', 'snack', 'dessert', 'drink', 'sauce', 'other'];
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
                $product->setImage($item['image']);
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
            ['name' => 'Margherita 12"', 'description' => 'Tomato Sauce, Fior di Latte Mozzarella, Fresh Basil', 'price' => 10.5, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Calabrian Buffalo Chicken 12"', 'description' => 'Roasted Chicken, Fresh Mozzarella, Gorgonzola, Calabrian Buffalo Sauce, Green Onions', 'price' => 14, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Vegan 12"', 'description' => 'Tomato Sauce, House Pesto, Roasted Shrooms, Baby Arugula, Roasted Squash, Caramelized Onion', 'price' => 12.99, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Bruce’s Seasonal Special', 'description' => 'Our White Sauce, Fresh Mozzarella, Roasted Chicken, Spinach, Garlic, Roasted Bell Peppers', 'price' => 17, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Margherita 16"', 'description' => 'Tomato Sauce, Fior di Latte Mozzarella, Fresh Basil', 'price' => 13.5, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Four Seasons', 'description' => 'Roasted Chicken, Fresh Mozzarella, Gorgonzola, Calabrian Buffalo Sauce, Green Onions', 'price' => 14, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Pepperoni', 'description' => 'Tomato Sauce, House Pesto, Roasted Shrooms, Baby Arugula, Roasted Squash, Caramelized Onion', 'price' => 12.99, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Caramelized Zucchini Flatbread', 'description' => 'Our White Sauce, Fresh Mozzarella, Roasted Chicken, Spinach, Garlic, Roasted Bell Peppers', 'price' => 17, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Sheet-Pan Pizza with Brussels Sprouts', 'description' => 'Tomato Sauce, Fior di Latte Mozzarella, Fresh Basil', 'price' => 10.5, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Salad Pizza', 'description' => 'Roasted Chicken, Fresh Mozzarella, Gorgonzola, Calabrian Buffalo Sauce, Green Onions', 'price' => 14, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Pizza with Fennel and Sausage', 'description' => 'Tomato Sauce, House Pesto, Roasted Shrooms, Baby Arugula, Roasted Squash, Caramelized Onion', 'price' => 12.99, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
            ['name' => 'Herby Pizza', 'description' => 'Our White Sauce, Fresh Mozzarella, Roasted Chicken, Spinach, Garlic, Roasted Bell Peppers', 'price' => 17, 'image' => 'https://images.all-free-download.com/images/graphiclarge/pizza_hd_picture_5_167275.jpg'],
        ];
        $this->initProduct('pizza', $items);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function initDrinks() {
        $items = [
            ['name' => 'Water 0.5L', 'description' => '', 'price' => 1.49, 'image' => 'https://torange.biz/photo/19/IMAGE/everyday-home-life-beverages-drinking-water-19987.jpg'],
            ['name' => 'Cola 0.33L', 'description' => '', 'price' => 2.20, 'image' => 'https://www.publicdomainpictures.net/pictures/300000/nahled/coke-with-ice-1556881290rXk.jpg'],
            ['name' => 'Cappuccino 0.4L', 'description' => '', 'price' => 2, 'image' => 'https://lh3.googleusercontent.com/proxy/iPYDn3ZJQgjH1oznEdt295g4FQuQW9stFyA5Qe7u_OgfhPk9_AzdlZtusPccAJ9pZDpsgn0GHO_q7hf8qbGGG3J6nUbWFPNUG3x-ytOpJQzGudLfzeiLbSXE0XAtryNW9OfR-PWLdThXnvfSptIza3_mz2SibRyO5VbTX2Q2KafBm8pQeqLM']
        ];
        $this->initProduct('drink', $items);
    }

    private function initDesserts() {
        $items = [
            ['name' => 'Cheesecake', 'description' => '', 'price' => 2.51, 'image' => 'https://c1.peakpx.com/wallpaper/311/552/43/cake-delicious-platemchina-topping-wallpaper-preview.jpg'],
            ['name' => 'Carrot cake', 'description' => '', 'price' => 2.33, 'image' => 'https://c1.staticflickr.com/1/919/43124953861_7374e8b2ff_b.jpg'],
            ['name' => 'Brownies', 'description' => '', 'price' => 1.90, 'image' => 'https://live.staticflickr.com/3199/2729305807_c6be2c9b16_b.jpg']
        ];
        $this->initProduct('dessert', $items);
    }

    protected function initSauce() {
        $items = [
            ['name' => 'Ketchup', 'description' => '', 'price' => 0.35, 'image' => 'https://c2.peakpx.com/wallpaper/837/637/124/2k-food-spice-wallpaper-preview.jpg'],
            ['name' => 'Mayo', 'description' => '', 'price' => 2, 'image' => 'https://torange.biz/photo/9/IMAGE/health-sauces-condiments-mayonnaise-9120.jpg']
        ];
        $this->initProduct('sauce', $items);
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
        $this->initDesserts();
        $this->initSauce();
        return 0;
    }
}
