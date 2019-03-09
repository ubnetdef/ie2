FROM ubuntu:18.04

WORKDIR /srv/ie2

RUN apt-get update \
	&&  apt-get install -y \
		curl \
		git \
		nginx \
		software-properties-common \
		unzip \
		zip \
	&& add-apt-repository ppa:ondrej/php \
	&& apt-get update \
	&& DEBIAN_FRONTEND=noninteractive apt-get install -y \
		php7.1 \
		php7.1-curl \
		php7.1-fpm \
		php7.1-mbstring \
		php7.1-mcrypt \
		php7.1-mysql \
		php7.1-simplexml \
	&& rm -rf /var/lib/apt/lists/*

RUN curl -o /usr/local/bin/composer https://getcomposer.org/composer.phar \
	&& chmod +x /usr/local/bin/composer

COPY composer.json .
RUN composer install --no-dev

RUN rm /etc/nginx/sites-enabled/*
COPY nginx-ie2.conf /etc/nginx/sites-enabled/ie2.conf

COPY . .
RUN chown -R www-data tmp

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log

EXPOSE 80

ENTRYPOINT ["/srv/ie2/docker-entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]
