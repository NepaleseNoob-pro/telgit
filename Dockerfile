# Use the official PHP 8.2 image with an Apache web server
FROM php:8.2-apache

# Copy the application files from the repository to the web root of the container
COPY . /var/www/html/

# The base image already configures Apache to start, so no further command is needed.
