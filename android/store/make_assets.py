"""Genere les images de la fiche Play Store : icone 512, image de presentation 1024x500, captures 1080x2160.
Usage : python make_assets.py <dossier_captures_brutes>"""
import sys, os
from PIL import Image, ImageDraw, ImageFont, ImageFilter

OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)), "images")
os.makedirs(OUT, exist_ok=True)
PINK = (233, 30, 99)
SKY = (79, 195, 247)

def hourglass(size, bg=SKY, radius=0):
    S = 4  # supersampling
    W = size * S
    im = Image.new("RGBA", (W, W), (0, 0, 0, 0))
    d = ImageDraw.Draw(im)
    if bg:
        d.rounded_rectangle([0, 0, W - 1, W - 1], radius=radius * S, fill=bg)
    u = W / 100
    dark, glass, sand = (52, 64, 84), (255, 255, 255), (247, 196, 75)
    # verre : deux bulbes
    top = [(30*u, 22*u), (70*u, 22*u), (70*u, 34*u), (53*u, 50*u), (47*u, 50*u), (30*u, 34*u)]
    bot = [(47*u, 50*u), (53*u, 50*u), (70*u, 66*u), (70*u, 78*u), (30*u, 78*u), (30*u, 66*u)]
    for poly in (top, bot):
        d.polygon(poly, fill=glass)
    d.rounded_rectangle([30*u, 22*u, 70*u, 40*u], radius=6*u, fill=glass)
    d.rounded_rectangle([30*u, 60*u, 70*u, 78*u], radius=6*u, fill=glass)
    # sable
    d.polygon([(33*u, 34*u), (67*u, 34*u), (52*u, 48*u), (48*u, 48*u)], fill=sand)
    d.rectangle([49*u, 48*u, 51*u, 70*u], fill=sand)
    d.rounded_rectangle([33*u, 68*u, 67*u, 75*u], radius=3*u, fill=sand)
    d.pieslice([38*u, 63*u, 62*u, 75*u], 180, 360, fill=sand)
    # reflet
    d.rectangle([35*u, 25*u, 64*u, 26.5*u], fill=dark)
    # montants haut / bas
    d.rounded_rectangle([24*u, 16*u, 76*u, 22*u], radius=3*u, fill=dark)
    d.rounded_rectangle([24*u, 78*u, 76*u, 84*u], radius=3*u, fill=dark)
    return im.resize((size, size), Image.LANCZOS)

def font(size, bold=True):
    for f in (["C:/Windows/Fonts/segoeuib.ttf", "C:/Windows/Fonts/arialbd.ttf"] if bold
              else ["C:/Windows/Fonts/segoeui.ttf", "C:/Windows/Fonts/arial.ttf"]):
        if os.path.exists(f):
            return ImageFont.truetype(f, size)
    return ImageFont.load_default()

# 1. Icone 512x512 (le Play Store arrondit lui-meme les coins)
hourglass(512).convert("RGB").save(os.path.join(OUT, "icon-512.png"))

# 2. Captures 1080x2160 (ratio max 2:1) : on retire barre d'etat et barre de navigation
raw = sys.argv[1] if len(sys.argv) > 1 else None
shots = []
if raw:
    for i, name in enumerate(sorted(f for f in os.listdir(raw) if f.startswith("s") and f.endswith(".png")), 1):
        im = Image.open(os.path.join(raw, name)).convert("RGB")
        im = im.crop((0, 108, 1080, 108 + 2160))
        p = os.path.join(OUT, f"screenshot-{i}.png")
        im.save(p)
        shots.append(im)

# 3. Image de presentation 1024x500
fg = Image.new("RGB", (1024, 500), PINK)
d = ImageDraw.Draw(fg)
for x in range(1024):  # degrade rose -> violet
    t = x / 1023
    d.line([(x, 0), (x, 499)], fill=(int(233 - 120 * t), int(30 + 10 * t), int(99 + 80 * t)))
ic = hourglass(150, radius=34)
fg.paste(ic, (60, 90), ic)
d.text((60, 270), "MyGames", font=font(78), fill="white")
d.text((62, 370), "Jeux d'ambiance entre amis,\nsur un seul téléphone", font=font(32, False), fill=(255, 235, 242), spacing=8)
for k, im in enumerate(shots[:3]):
    w = 230
    th = im.resize((w, int(w * im.height / im.width)), Image.LANCZOS)
    x, y = 520 + k * 165, 40 + (k % 2) * 40
    mask = Image.new("L", th.size, 0)
    ImageDraw.Draw(mask).rounded_rectangle([0, 0, th.width - 1, th.height - 1], radius=22, fill=255)
    sh = Image.new("RGBA", (th.width + 40, th.height + 40), (0, 0, 0, 0))
    ImageDraw.Draw(sh).rounded_rectangle([20, 24, th.width + 20, th.height + 24], radius=22, fill=(0, 0, 0, 90))
    sh = sh.filter(ImageFilter.GaussianBlur(10))
    fg.paste(sh, (x - 20, y - 20), sh)
    fg.paste(th, (x, y), mask)
fg.save(os.path.join(OUT, "feature-graphic-1024x500.png"))
print("OK ->", OUT)
