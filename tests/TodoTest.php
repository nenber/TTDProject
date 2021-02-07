<?php

namespace App\Tests;

use App\Entity\Item;
use App\Entity\TodoList;
use App\Entity\User;
use App\Service\ToDoListService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoTest extends WebTestCase
{

    /**
     * @var User
     */
    private static $user;

    /**
     * @var EntityManagerInterface
     */
    private static $em;

    /**
     * @var \Faker\Generator
     */
    private static $faker;

    /**
     * @var TodoListService
     */
    private $listService;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
        parent::setUpBeforeClass();

        /** @var EntityManagerInterface em */
        self::$em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        /** @var User user */
        self::$user = new User("a@a.fr", "first","last","azertyuii", new \DateTime("1997-01-01"));

        /** @var Generator faker */
        self::$faker = Factory::create();
    }

    public function setUp(): void
    {
        self::bootKernel();
        $this->listService = self::$kernel->getContainer()->get('app.service.todolist');
        if (!is_null(self::$user))
            self::$user->removeTodoList();
    }

    public function testCreateTodo()
    {
        $this->listService->create(self::$user);
        self::assertNotNull(self::$user->getTodoList());
    }

    public function testCreateTodoNotUnique()
    {
        $this->listService->create(self::$user);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Vous avez déjà créer une liste");
        $this->expectExceptionCode(500);

        $this->listService->create(self::$user);
    }

    public function testInsertTenTodo()
    {
        $this->listService->create(self::$user);

        /**
         * @var $todo TodoList
         */
        $todo = self::$user->getTodoList();

        $items = [];

        for ($i = 0; $i < 10; $i++) {
            $items[] = new Item(
                join(" ", self::$faker->words),
                self::$faker->text(),
                (new \DateTime())->modify('+' . (40 * ($i + 1)) . ' minutes'));
        }

        /**
         * @var $todoItem Item
         */
        foreach ($items as $key => $todoItem) {
            $todo = $this->listService->insert($todo, $todoItem);
        }

        self::assertEquals(10, $todo->getItems()->count());
    }

    public function testInsertToMuchTodo()
    {
        $this->listService->create(self::$user);

        /**
         * @var $todo TodoList
         */
        $todo = self::$user->getTodoList();

        $items = [];

        for ($i = 0; $i < 11; $i++) {
            $items[] = new Item(
                join(" ", self::$faker->words),
                self::$faker->text(),
                (new \DateTime())->modify('+' . (40 * ($i + 1)) . ' minutes'));
        }

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Vous avez déjà le nombre maximum de tâche");
        $this->expectExceptionCode(500);

        /**
         * @var $todoItem Item
         */
        foreach ($items as $key => $todoItem) {
            $todo = $this->listService->insert($todo, $todoItem);
        }
    }

    public function testAddToFast()
    {
        $this->listService->create(self::$user);

        /**
         * @var $todo TodoList
         */
        $todo = self::$user->getTodoList();

        $items = [];

        for ($i = 0; $i < 2; $i++) {
            $items[] = new Item(
                join(" ", self::$faker->words),
                self::$faker->text(),
                (new \DateTime())->modify('+' . (10 * ($i + 1)) . ' minutes'));
        }

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Vous devez avoir 30 minutes de différences entre chaque ajout");
        $this->expectExceptionCode(500);

        /**
         * @var $todoItem Item
         */
        foreach ($items as $key => $todoItem) {
            $todo = $this->listService->insert($todo, $todoItem);
        }
    }
}
