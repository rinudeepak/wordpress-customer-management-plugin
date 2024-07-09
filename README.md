# WordPress Customer Management Plugin

## Project Overview
This WordPress plugin manages a custom database of customer information with comprehensive administrative capabilities including adding, editing, deleting, and viewing customer records.

## Installation
1. Download the plugin files.
2. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress.
4. Import the SQL dump file (`customer-table-dump.sql`) into your WordPress database to create the necessary table and insert dummy data.

### How to Import the SQL Dump File

 **Using phpMyAdmin:**
   - Log in to your phpMyAdmin.
   - Select your WordPress database.
   - Click on the `Import` tab.
   - Choose the `customer-table-dump.sql` file from the plugin's `assets` directory.
   - Click on the `Go` button to import the SQL dump.

 **Using MySQL Command Line:**
   - Open your command line or terminal.
   - Navigate to the directory containing the SQL dump file.
   - Run the following command (replace `your_database_name` with your actual database name):
     ```
     mysql -u your_username -p your_database_name < assets/customer-table-dump.sql
     ```
   - Enter your database password when prompted.

 **Using MySQL Workbench:**
   - Open MySQL Workbench.
   - Connect to your database.
   - Click on `Server > Data Import`.
   - Select `Import from Self-Contained File`.
   - Choose the `customer-table-dump.sql` file.
   - Select your target database.
   - Click on `Start Import`.

Once the SQL dump file is imported, the `wp_customer` table will be created and populated with dummy data for testing.

## Usage
1. Navigate to the 'Customer Management' menu in the WordPress admin dashboard.
2. Use the provided forms to add, edit, delete, or view customer records.

## Contributing
1. Fork the repository.
2. Create a new branch (`git checkout -b feature/YourFeature`).
3. Commit your changes (`git commit -am 'Add some feature'`).
4. Push to the branch (`git push origin feature/YourFeature`).
5. Create a new Pull Request.

