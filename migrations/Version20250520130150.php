<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520130150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE task ADD COLUMN difficulty INTEGER DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password FROM user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , password VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO user (id, email, roles, password) SELECT id, email, roles, password FROM __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__task AS SELECT id, title, description, is_done, created_at FROM task
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE task
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, is_done BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO task (id, title, description, is_done, created_at) SELECT id, title, description, is_done, created_at FROM __temp__task
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__task
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password FROM user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , password VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO user (id, email, roles, password) SELECT id, email, roles, password FROM __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)
        SQL);
    }
}
