import openpyxl
import datetime
import re
import json
import os
import warnings
warnings.filterwarnings('ignore')

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
EXCEL_PATH = os.path.join(BASE_DIR, 'Training Dinas TN.xlsx')
OUTPUT_PATH = os.path.join(BASE_DIR, 'database', 'excel_import.json')

wb = openpyxl.load_workbook(EXCEL_PATH, data_only=True)
ws = wb['TrainingData']

headers = {c: ws.cell(2, c).value for c in range(1, ws.max_column + 1)}

# Kolom yang PAIRED (tanggal + status): nama sertifikasi ada di kolom + status ada di kolom berikutnya
# Kolom 08-26 (Human Factor -> DG Status) = paired
# Kolom setelah 26 (col 27 ke atas) = single date only = certification TANPA EXPIRY

EXPANDING_COLS = 68

def parse_date(v):
    if v is None:
        return None
    if isinstance(v, (datetime.datetime, datetime.date)):
        return v.strftime('%Y-%m-%d')
    if isinstance(v, (int, float)):
        try:
            dt = datetime.datetime(1899, 12, 30) + datetime.timedelta(days=int(v))
            return dt.strftime('%Y-%m-%d')
        except:
            pass
    s = str(v).strip()
    m1 = re.search(r'(\d{4})[-/](\d{1,2})[-/](\d{1,2})', s)
    if m1:
        try:
            return f"{int(m1.group(1)):04d}-{int(m1.group(2)):02d}-{int(m1.group(3)):02d}"
        except:
            pass
    return None

def is_valid_training(v):
    s = str(v).strip().upper() if v else ''
    return s in ('VALID', 'EXPIRING', 'EXPIRED')

data = []
for row_idx in range(3, ws.max_row + 1):
    emp_id = ws.cell(row_idx, 2).value
    emp_name = ws.cell(row_idx, 3).value
    unit = ws.cell(row_idx, 6).value or ws.cell(row_idx, 7).value or 'TN'
    
    if not (emp_id and emp_name):
        continue
    
    emp_id_str = str(emp_id).strip()
    emp_name_str = str(emp_name).strip()
    unit_str = str(unit).strip()
    
    certs = []
    
    # Kolom 08-26: paired date + status columns
    # Setiap pasangan: DATE_COL + STATUS_COL
    for c in range(8, 27):
        if c > EXPANDING_COLS:
            break
        cert_name = headers.get(c)
        status_col = c + 1
        if not cert_name:
            continue
        
        date_val = ws.cell(row_idx, c).value
        status_val = ws.cell(row_idx, status_col).value
        
        parsed_date = parse_date(date_val)
        status_str = str(status_val).strip().upper() if status_val else ''
        
        if not is_valid_training(status_val):
            # No training record or blank - skip
            continue
        
        # Ada tanggal dan status Valid/Expiring/Expired
        # Simpan juga status asli dari Excel sebagai sumber kebenaran (override perhitungan tanggal)
        if parsed_date:
            certs.append({
                'name': cert_name,
                'expiry_date': parsed_date,
                'issue_date': None,  # Akan dihitung otomatis lewat seeder 2 tahun sebelum expiry
                'status': status_str.lower(),  # valid / expiring / expired dari kolom Status Excel
            })
    
    # Kolom 28-68: tanggal tunggal, NO status = SERTIFIKASI TANPA EXPIRY (Permanen/Forever)
    # Bug user: ini sertifikasi yang TIDAK PUNYA expired - hanya tanggal saja (date-only)
    for c in range(28, EXPANDING_COLS + 1):
        cert_name = headers.get(c)
        date_val = ws.cell(row_idx, c).value
        
        if not cert_name:
            continue
        
        # Abaikan jika sudah ada di daftar (yang paired cols)
        if any(cert['name'] == cert_name for cert in certs):
            continue
        
        parsed_date = parse_date(date_val)
        
        # Jika ada tanggal tapi tidak ada status => sertifikat Tanpa Expiry
        if parsed_date:
            certs.append({
                'name': cert_name,
                'expiry_date': None,  # TIDAK ADA EXPIRY
                'issue_date': parsed_date,
                'status': None,
            })
    
    if certs:
        data.append({
            'employee_number': emp_id_str,
            'name': emp_name_str,
            'unit': unit_str,
            'certs': certs,
        })

total = sum(len(d['certs']) for d in data)
print(f"Parsed {len(data)} employees with {total} certifications.")

with open(OUTPUT_PATH, 'w', encoding='utf-8') as f:
    json.dump(data, f, ensure_ascii=False, indent=2)

print("Saved to database/excel_import.json")