<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250717053836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_type ALTER price TYPE INT');
        $this->addSql('ALTER TABLE ticket_type ALTER available_from SET NOT NULL');
        $this->addSql('ALTER TABLE ticket_type ALTER available_to SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ticket_type ALTER price TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE ticket_type ALTER available_from DROP NOT NULL');
        $this->addSql('ALTER TABLE ticket_type ALTER available_to DROP NOT NULL');
    }
}
