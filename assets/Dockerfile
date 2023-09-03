FROM node:14

WORKDIR /app
COPY . /app/
RUN npm install -g @vue/cli
RUN npm install @vue/cli-service --save-dev
RUN npm  install
RUN npm run build
CMD ["npm", "run", "serve"]