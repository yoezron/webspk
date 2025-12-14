import os
from datetime import datetime
import qrcode
from PIL import Image, ImageDraw, ImageFont
from io import BytesIO

def generate_member_number():
    """Generate unique member number based on timestamp"""
    timestamp = datetime.now().strftime('%Y%m%d%H%M%S')
    return f'SPK-{timestamp}'

def generate_member_card(member):
    """Generate member card as PDF or image"""
    # Create card image
    card_width = 800
    card_height = 500
    card = Image.new('RGB', (card_width, card_height), 'white')
    draw = ImageDraw.Draw(card)
    
    # Add border
    draw.rectangle([(10, 10), (card_width-10, card_height-10)], outline='black', width=3)
    
    # Add header
    draw.rectangle([(10, 10), (card_width-10, 100)], fill='#2c3e50')
    
    # Try to use a font, fallback to default
    try:
        title_font = ImageFont.truetype('/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf', 24)
        text_font = ImageFont.truetype('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', 18)
        small_font = ImageFont.truetype('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', 14)
    except:
        title_font = ImageFont.load_default()
        text_font = ImageFont.load_default()
        small_font = ImageFont.load_default()
    
    # Title
    draw.text((card_width//2, 55), 'KARTU ANGGOTA', fill='white', font=title_font, anchor='mm')
    draw.text((card_width//2, 80), 'Serikat Pekerja Kampus', fill='white', font=small_font, anchor='mm')
    
    # Member details
    y_offset = 130
    line_height = 35
    
    details = [
        f'Nomor Anggota: {member.member_number}',
        f'Nama: {member.full_name}',
        f'Departemen: {member.department or "-"}',
        f'Posisi: {member.position or "-"}',
        f'Tanggal Bergabung: {member.join_date.strftime("%d/%m/%Y")}'
    ]
    
    for detail in details:
        draw.text((30, y_offset), detail, fill='black', font=text_font)
        y_offset += line_height
    
    # Generate QR code
    qr = qrcode.QRCode(version=1, box_size=10, border=2)
    qr.add_data(f'SPK-{member.member_number}')
    qr.make(fit=True)
    qr_img = qr.make_image(fill_color="black", back_color="white")
    qr_img = qr_img.resize((120, 120))
    
    # Paste QR code
    card.paste(qr_img, (card_width - 160, card_height - 160))
    
    # Save card
    filename = f'card_{member.member_number}.png'
    filepath = os.path.join('static', 'cards', filename)
    os.makedirs(os.path.dirname(filepath), exist_ok=True)
    card.save(filepath)
    
    return filepath
