## Building documentation

**Requirements**

- Fonts: `DejaVu Sans`, `DejaVu Sans Mono`
- pandoc
- pandoc luatex engine
- pandoc xetex engine

**Install dependencies for Ubuntu (22.04)**

```bash
sudo apt install pandoc texlive-latex-base texlive-fonts-recommended \
    texlive-extra-utils texlive-latex-extra texlive-xetex
```

**Build documentation**

Source: `docs/`
Output: `pdf/docs/`

```bash
make
```
