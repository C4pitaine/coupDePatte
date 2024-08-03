<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240803191641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adoption_indispensable (adoption_id INT NOT NULL, indispensable_id INT NOT NULL, INDEX IDX_97601362631C55DF (adoption_id), INDEX IDX_97601362365A2FC7 (indispensable_id), PRIMARY KEY(adoption_id, indispensable_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adoption_indispensable ADD CONSTRAINT FK_97601362631C55DF FOREIGN KEY (adoption_id) REFERENCES adoption (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adoption_indispensable ADD CONSTRAINT FK_97601362365A2FC7 FOREIGN KEY (indispensable_id) REFERENCES indispensable (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_indispensable DROP FOREIGN KEY FK_97601362631C55DF');
        $this->addSql('ALTER TABLE adoption_indispensable DROP FOREIGN KEY FK_97601362365A2FC7');
        $this->addSql('DROP TABLE adoption_indispensable');
    }
}
