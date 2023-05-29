# Building for Production


    docker build --no-cache docker/supersaas-cli/ -t supersaas-cli
    docker run -it --volume `pwd`:/opt/supersaas supersaas-cli composer install -ano
    docker run -it --volume `pwd`:/opt/supersaas supersaas-cli yarn
    docker run -it --volume `pwd`:/opt/supersaas supersaas-cli yarn build
