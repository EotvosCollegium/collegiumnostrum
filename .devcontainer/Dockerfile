FROM ubuntu:24.04

RUN apt-get update && apt-get upgrade -y && DEBIAN_FRONTEND=noninteractive TZ=Etc/UTC apt-get install -y \
    php8.3 php8.3-mysql php8.3-dom php8.3-gd php8.3-curl php8.3-zip curl composer make

RUN curl -sL 'https://deb.nodesource.com/setup_20.x' | bash - \
    && apt-get -y install nodejs \
    && ln -s /usr/bin/nodejs /usr/local/bin/node

CMD [ "sleep", "infinity" ]
