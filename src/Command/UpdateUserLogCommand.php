<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\UserLog;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-user-log',
)]
class UpdateUserLogCommand extends Command
{

    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

        protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '2048M');
        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            if ($user->getScore() < 100 || $user->getScore() > 900) {
                $user->setLog(true);
                $this->logUserChange($user);
            } else if ($user->getLog()) {
                $user->setLog(false);
            }
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }

        private function logUserChange(User $user): void
    {

        $userLog = new UserLog();
        $userLog->setUserId($user->getId());
        $userLog->setScore($user->getScore());
        $userLog->setCreatedAt(new DateTimeImmutable());

        $this->entityManager->persist($userLog);

        $this->logger->info("User {$user->getId()} был обновлен с log=true.");

    }

}