
FROM php:8.1-cli
WORKDIR /app
COPY . .
RUN apt-get update && apt-get install -y cron
COPY crontab.txt /etc/cron.d/auto-check
RUN chmod 0644 /etc/cron.d/auto-check
RUN crontab /etc/cron.d/auto-check
CMD ["cron", "-f"]
