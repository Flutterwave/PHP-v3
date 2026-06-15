FROM mcr.microsoft.com/devcontainers/base:0-focal

RUN apt-get update && \
    apt-get install -y software-properties-common && \
    add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y \
        php7.4 php7.4-cli php7.4-common php7.4-curl \
        php7.4-mysql php7.4-bcmath php7.4-soap php7.4-zip php7.4-intl \
        php7.4-gd php7.4-xsl php7.4-dom php7.4-mbstring \
        unzip && \
    rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer