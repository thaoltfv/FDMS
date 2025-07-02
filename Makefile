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
all: $(OUTDIR) $(PDFS)

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

.PHONY: all clean

