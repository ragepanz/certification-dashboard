import openpyxl
import datetime
import re
import json

wb = openpyxl.load_workbook(r'C:\Users\ipan\Project\dashboard_sertificate\Training Dinas TN.xlsx', data_only=True)
td = wb['TrainingData']

cert_cols = []
for c_idx in range(7, len(td[2])):
    name = td[2][c_idx].value
    if name and not str(name).endswith('Status') and str(name) not in [
        'Req Training', 'Completed Training', 'Achievement', 'Training Achivement', 
        'Employee Category', 'General Lic.', 'AMEL 1', 'AMEL 2', 'AMEL 3', 'AMEL No', 
        'AMEL Qty', 'CS No', 'Stamp No', 'Eligibility ACE', 'Eligibility SACE', '1', '2', '3', 'No'
    ]:
        cert_cols.append((c_idx, str(name).strip()))

def parse_date(v):
    if v is None:
        return None
    if isinstance(v, (datetime.datetime, datetime.date)):
        return v.strftime('%Y-%m-%d')
    if isinstance(v, (int, float)):
        try:
            # Excel base date
            dt = datetime.datetime(1899, 12, 30) + datetime.timedelta(days=int(v))
            return dt.strftime('%Y-%m-%d')
        except:
            pass
    s = str(v).strip()
    # YYYY-MM-DD or YYYY/MM/DD
    m1 = re.search(r'(\d{4})[-/](\d{1,2})[-/](\d{1,2})', s)
    if m1:
        try:
            return f"{int(m1.group(1)):04d}-{int(m1.group(2)):02d}-{int(m1.group(3)):02d}"
        except:
            pass
    # DD/MM/YYYY or DD-MM-YYYY
    m2 = re.search(r'(\d{1,2})[-/](\d{1,2})[-/](\d{4})', s)
    if m2:
        try:
            return f"{int(m2.group(3)):04d}-{int(m2.group(2)):02d}-{int(m2.group(1)):02d}"
        except:
            pass
    return None


data = []
for row in td.iter_rows(min_row=3):
    emp_id = row[1].value
    emp_name = row[2].value
    job_title = row[3].value
    unit = row[5].value or row[6].value or 'TN'
    
    if emp_id and emp_name:
        emp_id_str = str(emp_id).strip()
        emp_name_str = str(emp_name).strip()
        unit_str = str(unit).strip()
        job_str = str(job_title).strip() if job_title else ''
        
        emp_certs = []
        for c_idx, cert_name in cert_cols:
            raw_val = row[c_idx].value
            dt_str = parse_date(raw_val)
            if dt_str:
                emp_certs.append({
                    'name': cert_name,
                    'expiry_date': dt_str
                })
        
        data.append({
            'employee_number': emp_id_str,
            'name': emp_name_str,
            'unit': unit_str,
            'job_title': job_str,
            'certs': emp_certs
        })

total_certs = sum(len(e['certs']) for e in data)
print(f"Parsed {len(data)} employees with total {total_certs} certificates.")

with open(r'c:\Users\ipan\Project\dashboard_sertificate\database\excel_import.json', 'w', encoding='utf-8') as f:
    json.dump(data, f, ensure_ascii=False, indent=2)

print("Saved to database/excel_import.json successfully!")
