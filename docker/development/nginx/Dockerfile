FROM nginx

WORKDIR /tmp

# Z-Ray assets
RUN apt-get update \
    && apt-get install -y curl \
    unzip \
    && curl -L  http://downloads.zend.com/zray/1208/zray-php5.6-Ubuntu-14.04-x86_64.tar.gz | tar zx \
    && mv /tmp/zray /opt/zray \
    && chown -R www-data:www-data /opt/zray \
    && rm -r /var/lib/apt/lists/*

# Z-Ray Plugins
RUN curl -o doctrine2.zip -L https://github.com/sandrokeil/Z-Ray-Doctrine2/archive/master.zip \
    && unzip -o /tmp/doctrine2.zip -d /opt/zray/runtime/var/plugins/ \
    && mv /opt/zray/runtime/var/plugins/Z-Ray-Doctrine2-master /opt/zray/runtime/var/plugins/doctrine2
