FROM ubuntu:22.04

RUN apt-get update && apt-get upgrade -y && DEBIAN_FRONTEND=noninteractive TZ=Etc/UTC apt-get install -y \
    php8.1 php8.1-mysql php8.1-dom php8.1-gd php8.1-curl php8.1-zip curl composer make

RUN curl -sL 'https://deb.nodesource.com/setup_20.x' | bash - \
    && apt-get -y install nodejs \
    && ln -s /usr/bin/nodejs /usr/local/bin/node

CMD [ "sleep", "infinity" ]
