<?php

namespace App\Tests;

use App\Entity\TodoList;
use App\Entity\User;
use App\Service\ToDoListService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
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
        self::$em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        self::$user = self::$em->getRepository(User::class)->findOneBy(["email" => "email@email.fr"]);
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
        self::$user = $this->listService->create(self::$user);

        $todo = self::$user->getTodoList();

        $item = (new TodoListItem())
            ->setName(join(" ", self::$faker->words))
            ->setContent(self::$faker->text());

        $todo = $this->listService->insert($todo, $item);

        self::$user->setTodoList($todo);

        self::assertEquals($todo, self::$user->getTodoList());
    }

    public function testCreateTodoNotUnique()
    {
        self::$user = $this->listService->create(self::$user);

        $todo = self::$user->getTodoList();

        $item = (new TodoListItem())
            ->setName(join(" ", self::$faker->words))
            ->setContent(self::$faker->text());

        $this->listService->insert($todo, $item);

        self::assertEquals($todo, self::$user->getTodoList());
    }

    public function testCreateTwoTodo()
    {
        $user = self::$em->getRepository(User::class)->findOneBy(["email" => "email@email2.fr"]);

        try {
            $this->listService->create($user);
        } catch (\Exception $e) {
            self::assertEquals(500, $e->getCode());
            self::assertEquals("Vous ne devez avoir qu'une todolist", $e->getMessage());
            return;
        }

        self::fail();
    }

    public function testInsertTenTodo()
    {
        self::$user = $this->listService->create(self::$user);

        /**
         * @var $todo TodoList
         */
        $todo = self::$user->getTodoList();

        $items = [];

        for ($i = 0; $i < 10; $i++) {
            $items[] = (new TodoListItem())
                ->setName(join(" ", self::$faker->words))
                ->setContent(self::$faker->text())
                ->setCreatedAt((new \DateTime())->modify('+' . (40 * ($i + 1)) . ' minutes'));
        }

        /**
         * @var $todoItem TodoListItem
         */
        foreach ($items as $key => $todoItem) {
            $todo = $this->listService->insert($todo, $todoItem);
        }

        self::assertEquals(10, $todo->getTodoListItems()->count());
    }

    public function testInsertToMuchTodo()
    {
        self::$user = $this->listService->create(self::$user);

        /**
         * @var $todo TodoList
         */
        $todo = self::$user->getTodoList();

        $items = [];

        for ($i = 0; $i < 11; $i++) {
            $items[] = (new TodoListItem())
                ->setName(join(" ", self::$faker->words))
                ->setContent(self::$faker->text())
                ->setCreatedAt((new \DateTime())->modify('+' . (40 * ($i + 1)) . ' minutes'));
        }

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(500);

        /**
         * @var $todoItem TodoListItem
         */
        foreach ($items as $key => $todoItem) {
            $todo = $this->listService->insert($todo, $todoItem);
        }

        self::assertEquals(10, $todo->getTodoListItems()->count());
    }

    public function testAddToFast()
    {
        self::$user = $this->listService->create(self::$user);

        /**
         * @var $todo TodoList
         */
        $todo = self::$user->getTodoList();

        $items = [];

        for ($i = 0; $i < 2; $i++) {
            $items[] = (new TodoListItem())
                ->setName(join(" ", self::$faker->words))
                ->setContent(self::$faker->text())
                ->setCreatedAt((new \DateTime())->modify('+' . (20 * ($i + 1)) . ' minutes'));
        }

        $this->expectException(\Exception::class);

        /**
         * @var $todoItem TodoListItem
         */
        foreach ($items as $key => $todoItem) {
            $todo = $this->listService->insert($todo, $todoItem);
        }
    }

    public function testMock()
    {

        $todo = (new TodoList())->setAuthor(self::$user);

        $mock = $this->getMockBuilder(User::class)
            ->onlyMethods(["getTodoList", "setTodoList"])
            ->getMock();

        $mock->method("getTodoList")->willReturn($todo);
        $mock->method("setTodoList")->willReturn(self::$user);

        $this->listService->create(self::$user);
        self::assertEquals($todo, self::$user->getTodoList());
        self::assertEquals(self::$user, self::$user->setTodoList($todo));
    }
}
