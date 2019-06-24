<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Comment;
use App\Entity\Articles;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR') ;

        //=> Créer 3 catégories faker en haut
        for($j =1;$j<=3;$j++){

            $category = new Category(); //=> mettre use

            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());

            $manager->persist($category);


            //=> Créer entre 4 et 6 articles
            for($i=1;$i<=mt_rand(4,6);$i++){

                $article = new Articles();
                $content = '<p>'.join($faker->paragraphs(5),'</p><p>').'</p>';

                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImg($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('- 6 months'))
                        ->setCategory($category);
    
                        $manager->persist($article);
        
                        //=> on donne des commentaires à l'article        
                        for($k=1;$k<=mt_rand(4,10);$k++){
                            $comment = new Comment();
                            $content = '<p>'.join($faker->paragraphs(2),'</p><p>').'</p>';
                            
                            $now = new \DateTime();
                            $interval = $now->diff($article->getCreatedAt());
                            $days = $interval->days;
                            $minimum = '-'.$days.' days'; //=> -100 days
                            $comment->setAuthor($faker->name)
                                    ->setContent($content)
                                    ->setCreatedAt($faker->dateTimeBetween($minimum))
                                    ->setArticle($article);
                    
                            $manager->persist($comment);
                        }
            }           
        }

        $manager->flush();
    }
}
