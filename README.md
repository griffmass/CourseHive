# Project Setup Guide

This guide will walk you through setting up the environment and dependencies for this project.

## Step 1: Download MongoDB

1. Go to the MongoDB download page: [MongoDB Community Download](https://www.mongodb.com/try/download/community).
2. Download the **current version 8.0.3**, for **Windows platform**, and choose the **MSI package**.
3. Follow the installation instructions to install MongoDB on your system.

## Step 2: Download VSCode

1. Download **Visual Studio Code (VSCode)** from the official website: [VSCode Download](https://code.visualstudio.com/).
2. Follow the installation instructions to install VSCode on your system.

## Step 3: Download XAMPP

1. Go to the XAMPP download page: [XAMPP Download](https://www.apachefriends.org/index.html).
2. Download the latest version of **XAMPP for Windows 8.2.12 (PHP 8.2.12)**.
3. Follow the installation instructions to install XAMPP.

## Step 4: Start Apache in XAMPP

1. Open the **XAMPP Control Panel**.
2. Click the **Start** button next to **Apache** to start the server.

## Step 5: Download MongoDB Driver for PHP

1. Go to the MongoDB driver page for PHP: [MongoDB PHP Driver](https://pecl.php.net/package/mongodb/1.20.0/windows).
2. Scroll down and select the **PHP 8.2** version (as you are using PHP 8.2.12 in XAMPP).
3. Choose **Thread Safe** and select **x64** architecture, then download the `.dll` file.

## Step 6: Install MongoDB Driver in XAMPP

1. After downloading the driver, open the **XAMPP Control Panel** and click **Explorer** to open the XAMPP folder.
2. Navigate to the `php` folder and find the `ext` folder inside it: `C:\xampp\php\ext`.
3. Copy the `php_mongodb.dll` file from the downloaded MongoDB driver and paste it into the `ext` folder: `C:\xampp\php\ext`.

## Step 7: Update PHP Configuration

1. Go back to the `php` folder and find the `php.ini` file.
2. Open the `php.ini` file and add the following line at the end:

   ```ini
   extension=mongodb

3. Make sure there are no semicolons (;) before the extension=mongodb line, as this would comment it out and prevent it from working.
4. Save the php.ini file.

## Step 8: Install Composer

1. Download Composer from the official website: Composer Download.
2. Click the Composer-Setup.exe to install the latest version of Composer on your system.
3. 
## Step 9: Install MongoDB PHP Library

1. Open the Command Prompt on your system.
2. Navigate to your Desktop:

```ini
cd Desktop
mkdir mongoDriver
cd mongoDriver
```
  Your path should look like this: ``` C:\Users\Griffmass\Desktop\mongoDriver. ```

Run the following Composer command to install the MongoDB PHP library:

```ini
composer require mongodb/mongodb
```

# Step 10: Clone the Project Repository

1. Navigate to the htdocs folder in XAMPP:
   ```ini
    cd C:/xampp/htdocs

2. Initialize a new git repository and clone the project:
   ```ini
   git init
   git clone https://github.com/griffmass/CourseHive

# Step 11: Install PHPMailer

1. Open the cloned project in VSCode.
2. Open the terminal in VSCode and run the following command to install PHPMailer:

  ```ini
composer require phpmailer/phpmailer
```
***

<h1>Conclusion</h1>

Once you've completed these steps, your development environment will be set up with MongoDB, PHP, Composer, and the necessary dependencies for the project. You can now proceed with developing and running the project on your local machine.




