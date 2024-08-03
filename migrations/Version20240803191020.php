<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240803191020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal_friandise (animal_id INT NOT NULL, friandise_id INT NOT NULL, INDEX IDX_EEAD64FE8E962C16 (animal_id), INDEX IDX_EEAD64FEC2F75B09 (friandise_id), PRIMARY KEY(animal_id, friandise_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal_friandise ADD CONSTRAINT FK_EEAD64FE8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal_friandise ADD CONSTRAINT FK_EEAD64FEC2F75B09 FOREIGN KEY (friandise_id) REFERENCES friandise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F5EB747A3');
        $this->addSql('DROP INDEX IDX_2EBCCA8F5EB747A3 ON suivi');
        $this->addSql('ALTER TABLE suivi CHANGE animal_id_id animal_id INT NOT NULL');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id)');
        $this->addSql('CREATE INDEX IDX_2EBCCA8F8E962C16 ON suivi (animal_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal_friandise DROP FOREIGN KEY FK_EEAD64FE8E962C16');
        $this->addSql('ALTER TABLE animal_friandise DROP FOREIGN KEY FK_EEAD64FEC2F75B09');
        $this->addSql('DROP TABLE animal_friandise');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F8E962C16');
        $this->addSql('DROP INDEX IDX_2EBCCA8F8E962C16 ON suivi');
        $this->addSql('ALTER TABLE suivi CHANGE animal_id animal_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F5EB747A3 FOREIGN KEY (animal_id_id) REFERENCES animal (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2EBCCA8F5EB747A3 ON suivi (animal_id_id)');
    }
}
