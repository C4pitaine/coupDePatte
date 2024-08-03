<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240803191512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal_indispensable (animal_id INT NOT NULL, indispensable_id INT NOT NULL, INDEX IDX_AC7615A48E962C16 (animal_id), INDEX IDX_AC7615A4365A2FC7 (indispensable_id), PRIMARY KEY(animal_id, indispensable_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal_indispensable ADD CONSTRAINT FK_AC7615A48E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal_indispensable ADD CONSTRAINT FK_AC7615A4365A2FC7 FOREIGN KEY (indispensable_id) REFERENCES indispensable (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal_indispensable DROP FOREIGN KEY FK_AC7615A48E962C16');
        $this->addSql('ALTER TABLE animal_indispensable DROP FOREIGN KEY FK_AC7615A4365A2FC7');
        $this->addSql('DROP TABLE animal_indispensable');
    }
}
