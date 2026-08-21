import mysql.connector

conn = mysql.connector.connect(user='root', password='', host='127.0.0.1', database='sertification')
cursor = conn.cursor(dictionary=True)

# Search "an" across name, certificate_name, employee_number
search = "an"
query = f"""
    SELECT c.id, c.certificate_name, u.name, u.employee_number 
    FROM certifications c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.certificate_name LIKE '%{search}%' 
       OR u.name LIKE '%{search}%' 
       OR u.employee_number LIKE '%{search}%'
"""
cursor.execute(query)
rows = cursor.fetchall()
print(f"Total matching certification rows for keyword '{search}': {len(rows)}")

cursor.execute(f"SELECT id, name, employee_number FROM users WHERE name LIKE '%{search}%' OR employee_number LIKE '%{search}%'")
user_rows = cursor.fetchall()
print(f"Total employees matching '{search}': {len(user_rows)}")
print("First 10 employees matched:")
for u in user_rows[:10]:
    print(f"- {u['name']} ({u['employee_number']})")
