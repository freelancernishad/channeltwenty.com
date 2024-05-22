# get_mac.py
import uuid

def get_mac_address():
    mac_address = ':'.join(['{:02x}'.format((uuid.getnode() >> elements) & 0xff)
                            for elements in range(0,2*6,2)][::-1])
    return mac_address

if __name__ == "__main__":
    print(get_mac_address())
