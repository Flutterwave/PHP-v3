FROM gitpod/workspace-full

RUN sudo pecl channel-update pecl.php.net && \
    sudo pecl install xdebug