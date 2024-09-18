<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240918110042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parrainage (id INT AUTO_INCREMENT NOT NULL, montant INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parrainage_user (parrainage_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E670402C70F2D188 (parrainage_id), INDEX IDX_E670402CA76ED395 (user_id), PRIMARY KEY(parrainage_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parrainage_animal (parrainage_id INT NOT NULL, animal_id INT NOT NULL, INDEX IDX_6B4DE16F70F2D188 (parrainage_id), INDEX IDX_6B4DE16F8E962C16 (animal_id), PRIMARY KEY(parrainage_id, animal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE parrainage_user ADD CONSTRAINT FK_E670402C70F2D188 FOREIGN KEY (parrainage_id) REFERENCES parrainage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parrainage_user ADD CONSTRAINT FK_E670402CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parrainage_animal ADD CONSTRAINT FK_6B4DE16F70F2D188 FOREIGN KEY (parrainage_id) REFERENCES parrainage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parrainage_animal ADD CONSTRAINT FK_6B4DE16F8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parrainage_user DROP FOREIGN KEY FK_E670402C70F2D188');
        $this->addSql('ALTER TABLE parrainage_user DROP FOREIGN KEY FK_E670402CA76ED395');
        $this->addSql('ALTER TABLE parrainage_animal DROP FOREIGN KEY FK_6B4DE16F70F2D188');
        $this->addSql('ALTER TABLE parrainage_animal DROP FOREIGN KEY FK_6B4DE16F8E962C16');
        $this->addSql('DROP TABLE parrainage');
        $this->addSql('DROP TABLE parrainage_user');
        $this->addSql('DROP TABLE parrainage_animal');
    }
}
