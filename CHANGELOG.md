# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-08-06

### Added
- Initial release of Duweet Personal Finance App
- Laravel 12 framework setup with MySQL database
- Hierarchical accounts structure with 3-level depth support
- Account types enum: Asset (AS), Income (IN), Expenses (EX), Spending (SP), Liability (LI)
- Database migration for accounts table with foreign key constraints
- Comprehensive account seeder with 47+ real-world accounts including:
  - Cash & Bank accounts (Cash, Bank Account, E-Wallet)
  - Income categories (Salary, Bonus, Freelance, etc.)
  - Expense categories (Housing, Transportation, Food, Healthcare)
  - Spending categories (Shopping, Entertainment, Travel, etc.)
  - Liability accounts (Credit Cards, Loans, Mortgage)
- Account constants class for type-safe database operations
- MySQL database configuration and connection
- Complete project documentation with installation guide
- Product Requirements Document (PRD)
- Git repository initialization

### Technical Features
- Self-referencing foreign key for parent-child account relationships
- Database indexes for optimal query performance
- Seeder system for easy data population
- Environment-based configuration
- Comprehensive error handling and validation

### Infrastructure
- Composer dependency management
- MySQL 8.0+ compatibility
- PHP 8.3+ support
- Git version control setup
- Complete README with troubleshooting guide

## Installation Requirements
- PHP 8.3+
- Composer
- MySQL 8.0+
- Node.js 16+ (for asset compilation)

## Database Schema
- `accounts` table with hierarchical structure
- Support for nested accounts up to 3 levels deep
- Foreign key constraints for data integrity
- Optimized indexes for performance

---

## How to Use This Changelog

This changelog documents all notable changes made to the Duweet project. Each version includes:

- **Added**: New features
- **Changed**: Changes in existing functionality  
- **Deprecated**: Soon-to-be removed features
- **Removed**: Removed features
- **Fixed**: Bug fixes
- **Security**: Security improvements

For detailed technical information, see the commit history in the Git repository.
