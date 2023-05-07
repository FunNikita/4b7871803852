<?php

class WikipediaGame
{
    private $players;
    private $startPage;
    private $finishPage;
    private $currentPage;

    public function __construct()
    {
        $this->players = [];
        $this->startPage = $this->generateRandomPage();
        $this->finishPage = $this->generateRandomPage([$this->startPage]);
        $this->currentPage = $this->startPage;
    }

    public function startGame()
    {
        $this->getNumberOfPlayers();
        $this->createPlayers();
        $this->playGame();
        $this->displayResults();
    }

    private function getNumberOfPlayers()
    {
        $numPlayers = readline("Введите количество игроков: ");
        $numPlayers = intval($numPlayers);

        while ($numPlayers <= 0) {
            echo "Неверное количество игроков. Попробуйте еще раз." . PHP_EOL;
            $numPlayers = readline("Введите количество игроков: ");
            $numPlayers = intval($numPlayers);
        }

        return $numPlayers;
    }

    private function createPlayers()
    {
        $numPlayers = $this->getNumberOfPlayers();

        for ($i = 1; $i <= $numPlayers; $i++) {
            $name = readline("Введите имя игрока {$i}: ");
            $player = new Player($name, $this->startPage);
            $this->players[] = $player;
        }
    }

    private function playGame()
    {
        $gameOver = false;

        while (!$gameOver) {
            foreach ($this->players as $player) {
                echo "---------------------" . PHP_EOL;
                echo "Ход игрока: " . $player->getName() . PHP_EOL;
                echo "Текущая страница: " . $player->getCurrentPage() . PHP_EOL;
                $reachablePages = $this->getReachablePages($player->getCurrentPage());

                if (empty($reachablePages)) {
                    echo "Нет доступных страниц для перехода. Игрок {$player->getName()} выбывает из игры." . PHP_EOL;
                    continue;
                }

                echo "Доступные страницы: " . implode(", ", $reachablePages) . PHP_EOL;

                $choice = readline("Выберите страницу для перехода: ");

                while (!in_array($choice, $reachablePages)) {
                    echo "Недопустимый выбор. Попробуйте еще раз." . PHP_EOL;
                    $choice = readline("Выберите страницу для перехода: ");
                }

                $player->setCurrentPage($choice);

                if ($player->getCurrentPage() === $this->finishPage) {
                    echo "Игрок {$player->getName()} достиг финишной страницы!" . PHP_EOL;
                    $gameOver = true;
                }
            }
        }
    }

    private function displayResults()
    {
        echo "Результаты игры:" . PHP_EOL;

        foreach ($this->players as $player) {
            echo "Игрок: " . $player->getName() . " - Число переходов: " . $player->getStepsToFinish() . PHP_EOL;
        }
    }

    private function generateRandomPage(array $excludedPages = [])
    {
        $pages = [
            "Page 1",
            "Page 2",
            "Page 3",
            // Можно добавить больше :)
        ];

        $availablePages = array_diff($pages, $excludedPages);
        $randomPage = $availablePages[array_rand($availablePages)];

        return $randomPage;
    }

    private function getReachablePages($currentPage)
    {
        $reachablePages = [];

        switch ($currentPage) {
            case "Page 1":
                $reachablePages = ["Page 2", "Page 3"];
                break;
            case "Page 2":
                $reachablePages = ["Page 1", "Page 3"];
                break;
            case "Page 3":
                $reachablePages = ["Page 1", "Page 2"];
                break;
        }

        return $reachablePages;
    }
}

class Player
{
    private $name;
    private $currentPage;
    private $stepsToFinish;

    public function __construct($name, $currentPage)
    {
        $this->name = $name;
        $this->currentPage = $currentPage;
        $this->stepsToFinish = 0;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function setCurrentPage($page)
    {
        $this->currentPage = $page;
        $this->stepsToFinish++;
    }

    public function getStepsToFinish()
    {
        return $this->stepsToFinish;
    }
}
