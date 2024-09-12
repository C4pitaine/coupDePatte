<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240912124503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F8E962C16');
        $this->addSql('DROP INDEX IDX_2EBCCA8F8E962C16 ON suivi');
        $this->addSql('ALTER TABLE suivi ADD animal VARCHAR(255) NOT NULL, DROP animal_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE suivi ADD animal_id INT NOT NULL, DROP animal');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2EBCCA8F8E962C16 ON suivi (animal_id)');
    }
}
