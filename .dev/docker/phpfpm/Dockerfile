FROM php:8.1.0-fpm AS dev_phpfpm
WORKDIR /var/www/html

# update the system
RUN apt update \
    && apt autoremove -y

# add local user
RUN useradd -u 1000 local

# install Symfony 5.1.x requirements
RUN apt update \
    && apt install -y software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt install -y git libzip-dev zlib1g-dev unzip libpcre3 libpcre3-dev libxml2-dev libxslt-dev libpng-dev vim \
    && docker-php-ext-install pdo_mysql xsl zip gd intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# opcache
RUN docker-php-ext-install opcache

# load PHP configurations
COPY ./phpfpm/config/php.ini /usr/local/etc/php/
