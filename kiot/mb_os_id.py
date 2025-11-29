import winreg as reg

def get_osid():
    try:
        key = reg.OpenKey(reg.HKEY_LOCAL_MACHINE, r"SOFTWARE\Microsoft\Cryptography", 0, reg.KEY_READ)
        machine_guid, _ = reg.QueryValueEx(key, "MachineGuid")
        reg.CloseKey(key)
        return machine_guid
    except Exception:
        return None

print("OSID:", get_osid())

import subprocess

def get_mbid():
    try:
        result = subprocess.run(
            ['wmic', 'baseboard', 'get', 'serialnumber'],
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            creationflags=0x08000000  # CREATE_NO_WINDOW
        )
        output = result.stdout.decode(errors='ignore').strip().split('\n')
        return output[1].strip() if len(output) > 1 else None
    except Exception as e:
        return None

print("MBID:", get_mbid())
