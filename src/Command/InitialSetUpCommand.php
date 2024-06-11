<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InitialSetUpCommand extends Command
{

    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $userPasswordHasher,
        ?string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('app:setup')
            ->setDescription('Setup application');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $user->setEmail('john.doe@example.com');
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, 'password')
        );
        $user->setRoles(['ROLE_USER']);
        $user->setName('John Doe');
        $user->setVerified(true);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Admin user is created successfully.');
        return Command::SUCCESS;
    }
}