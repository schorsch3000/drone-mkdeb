FROM debian:8
ENV DEBIAN_FRONTEND noninteractiv
RUN apt-get update
RUN apt-get install -y php5-cli wget git
RUN wget https://getcomposer.org/composer.phar -O /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer
ADD mkdeb /opt/mkdeb
RUN cd /opt/mkdeb; composer install
RUN ln -s /opt/mkdeb/mkDeb.php /usr/local/bin/mkDeb

