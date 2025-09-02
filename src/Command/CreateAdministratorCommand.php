<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-administrator',
    description: 'Allow you to create an administrator count',
    help: 'Allow you to create an administrator count'
)]
class CreateAdministratorCommand extends Command
{

    public function __construct(private readonly EntityManagerInterface $entityManager,private readonly UserPasswordHasherInterface $userPasswordHasher )
    {
        parent::__construct('app:create-administrator');

    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Address email')
            ->addArgument('password',InputArgument::OPTIONAL,'Password')
            ->addArgument('pseudo',InputArgument::OPTIONAL,'Pseudo')


        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('Write your address email : ');
            $email = $helper->ask($input,$output,$question);
        }
        $password = $input->getArgument('password');
        if (!$password) {
            $question = new Question('Write your password : ');
            $plainPassword = $helper->ask($input,$output,$question);
        }
        $pseudo = $input->getArgument('pseudo');
        if (!$pseudo) {
            $question = new Question('Write your pseudo : ');
            $pseudo = $helper->ask($input,$output,$question);
        }

        $admin = new User();
        $admin->setEmail($email)
               ->setPseudo($pseudo)
              ->setPassword($this->userPasswordHasher->hashPassword($admin,$plainPassword))
              ->setRoles(['ROLE_ADMIN'])
              ->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($admin);
        $this->entityManager->flush();


        $io->success('You have created an administrator count, don\'t forget to write clear in shell then enter !');
        return Command::SUCCESS;
    }
}
