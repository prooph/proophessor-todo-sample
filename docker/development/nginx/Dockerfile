FROM nginx

WORKDIR /tmp

# Z-Ray assets
RUN apt-get update \
    && apt-get install -y wget \
    && wget -nv -O - http://downloads.zend.com/zray/1208//zray-php5.6-Ubuntu-14.04-x86_64.tar.gz | tar zx \
    && mv /tmp/zray /opt/zray \
    && chown -R www-data:www-data /opt/zray \
    && rm -r /var/lib/apt/lists/*
