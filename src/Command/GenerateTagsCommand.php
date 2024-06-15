<?php

namespace App\Command;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class GenerateTagsCommand extends Command
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public SluggerInterface $slugger,
        public TagRepository $repository,
        ?string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('generate:tags')
            ->setHelp('This command allows you to generate random numbers of tags...')
            ->addArgument('num', InputArgument::REQUIRED, 'Numbers of tags to be generated')
            ->setDescription('Generate tags');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lastTag = $this->repository->findLast();

        $lastTagNo = filter_var($lastTag?->getName() ?? 0, FILTER_SANITIZE_NUMBER_INT) ?? 0;

        $number = $input->getArgument('num');

        for( $i = ($lastTagNo + 1); $i <= ($number + $lastTagNo); $i++ ) {
            $tag = new Tag();

            $tagName = 'Tag ' . $i;

            $tag->setName($tagName);
            $tag->setSlug( $this->slugger->slug($tagName) );
            $tag->setStatus(rand(0, 1));

            $this->entityManager->persist($tag);
        }

        $this->entityManager->flush();

        $output->write('Tags generated successfully');

        return Command::SUCCESS;
    }

}