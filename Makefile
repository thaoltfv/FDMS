# Makefile to convert all .md files in current dir to pdf/ using Pandoc

PANDOC = pandoc
SRC = $(wildcard docs/*.md)
OUTDIR = pdf
PDFS = $(SRC:%.md=$(OUTDIR)/%.pdf)

# -V mainfont="Noto Serif"
PANDOC_OPTS = --pdf-engine=xelatex \
	-V monofont="DejaVu Sans Mono" \
	-V geometry:margin=0.9in \
	--lua-filter=docs/scripts/hr-to-pagebreak.lua 


# Default target
usage:
	@echo "Usage:"
	@echo "  make docs     - Build PDF documentation from markdown files"
	@echo "  make clean    - Remove generated PDF files"
	@echo "  make dev      - Start development environment"
	@echo "  make logs     - Follow logs of the development environment"
	@echo "  make stop     - Stop the development environment"
	@echo "  make restart  - Restart the development environment"
	@echo "  make usage    - Show this help message"
	@echo ""
	@echo "The docs target will convert all .md files in docs/ to PDF format"
	@echo "using Pandoc with custom formatting options."

# Build the documentation
docs: $(OUTDIR) $(PDFS)

# Rule to compile each markdown file to pdf
$(OUTDIR)/%.pdf: %.md
	@echo "Compiling $< -> $@"
	$(PANDOC) $(PANDOC_OPTS) "$<" -o "$@"

# Create output directory if it doesn't exist
$(OUTDIR):
	mkdir -p $(OUTDIR)/docs

# Clean up generated PDFs
clean:
	rm -rf $(OUTDIR)

# Start the development environment
dev:
	./dev.sh up --build -d

logs:
	./dev.sh logs -f

# Stop the development environment
stop:
	./dev.sh down

# Restart the development environment
restart:
	./dev.sh restart


.PHONY: docs dev clean logs usage stop restart

