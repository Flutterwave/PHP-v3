.PHONY: init
test:
	@echo "Installing dependencies..."
	@composer install
	@echo "Installing dependencies... Done"
	@./vendor/bin/pest --coverage --min=0 --coverage-clover ./coverage.xml


