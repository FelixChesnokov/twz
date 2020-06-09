FROM centos:7

RUN yum -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm \
    && yum -y install http://rpms.famillecollet.com/enterprise/remi-release-7.rpm \
    && yum -y install yum-plugin-priorities yum-utils

RUN yum-config-manager --enable remi-php73
RUN yum-config-manager --setopt=remi-php73.priority=1 --save

RUN yum -y update && yum -y install php \
		php-pear \
		php-fpm \
		php-mbstring \
		php-mcrypt \
		php-mysqlnd \
		nano

RUN yum clean all && rm -rf /var/cache/yum

RUN mkdir -p /etc/php.d/extra
COPY www.ini /etc/php.d/
COPY www.conf /etc/php-fpm.d/
RUN mkdir -p /run/php-fpm

WORKDIR /var/www

VOLUME ["/var/www","/var/tmp", "/var/www/log/php-fpm"]

CMD ["php-fpm","-F"]
