# Use an appropriate Node.js image
FROM node:14

WORKDIR /app
COPY . /app/
# Install Vue CLI globally (if not already done)
RUN npm install -g @vue/cli
RUN npm  install
RUN npm run build
# Install project dependencies
COPY ./assets/package.json ./assets/package-lock.json ./
EXPOSE 8080
CMD ["npm", "run", "serve"]