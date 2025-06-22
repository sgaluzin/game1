FROM nginx:alpine

RUN apk update \
    && apk add --no-cache mc

CMD ["nginx", "-g", "daemon off;"]

