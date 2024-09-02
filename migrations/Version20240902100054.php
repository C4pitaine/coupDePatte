<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240902100054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favori (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favori_user (favori_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2529444EFF17033F (favori_id), INDEX IDX_2529444EA76ED395 (user_id), PRIMARY KEY(favori_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favori_animal (favori_id INT NOT NULL, animal_id INT NOT NULL, INDEX IDX_3BEBEF0AFF17033F (favori_id), INDEX IDX_3BEBEF0A8E962C16 (animal_id), PRIMARY KEY(favori_id, animal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favori_user ADD CONSTRAINT FK_2529444EFF17033F FOREIGN KEY (favori_id) REFERENCES favori (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favori_user ADD CONSTRAINT FK_2529444EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favori_animal ADD CONSTRAINT FK_3BEBEF0AFF17033F FOREIGN KEY (favori_id) REFERENCES favori (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favori_animal ADD CONSTRAINT FK_3BEBEF0A8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE favoris');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favoris (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, animal_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE favori_user DROP FOREIGN KEY FK_2529444EFF17033F');
        $this->addSql('ALTER TABLE favori_user DROP FOREIGN KEY FK_2529444EA76ED395');
        $this->addSql('ALTER TABLE favori_animal DROP FOREIGN KEY FK_3BEBEF0AFF17033F');
        $this->addSql('ALTER TABLE favori_animal DROP FOREIGN KEY FK_3BEBEF0A8E962C16');
        $this->addSql('DROP TABLE favori');
        $this->addSql('DROP TABLE favori_user');
        $this->addSql('DROP TABLE favori_animal');
    }
}
