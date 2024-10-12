docker run --rm --name ca-run -p 8080:80 -v $(pwd):/var/www/html -e CA_TEST=1 ca-base
