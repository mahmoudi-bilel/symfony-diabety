<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230305173942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC83FDE077');
        $this->addSql('ALTER TABLE commentaire CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC83FDE077 FOREIGN KEY (pub_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE publication ADD likes INT DEFAULT NULL, ADD dislikes INT DEFAULT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE numerodetel numerodetel VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC83FDE077');
        $this->addSql('ALTER TABLE commentaire CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC83FDE077 FOREIGN KEY (pub_id) REFERENCES publication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publication DROP likes, DROP dislikes, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE numerodetel numerodetel VARCHAR(255) DEFAULT NULL');
    }
}
