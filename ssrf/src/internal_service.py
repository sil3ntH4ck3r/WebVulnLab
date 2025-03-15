from flask import Flask, jsonify
import threading
import time

internal_app = Flask(__name__)

@internal_app.route('/health')
def health():
    return jsonify({'status': 'ok'})

@internal_app.route('/stats')
def stats():
    return jsonify({
        'requests_analyzed': stats_counter.get_count(),
        'malicious_detected': stats_counter.get_malicious_count(),
        'uptime': stats_counter.get_uptime()
    })

class StatsCounter:
    def __init__(self):
        self.total_count = 0
        self.malicious_count = 0
        self.start_time = time.time()
        self._lock = threading.Lock()
    
    def increment(self, is_malicious=False):
        with self._lock:
            self.total_count += 1
            if is_malicious:
                self.malicious_count += 1
    
    def get_count(self):
        with self._lock:
            return self.total_count
    
    def get_malicious_count(self):
        with self._lock:
            return self.malicious_count
    
    def get_uptime(self):
        return int(time.time() - self.start_time)

stats_counter = StatsCounter()

def run_internal_service():
    print("Iniciando servicio interno en puerto 8000...")
    internal_app.run(host='127.0.0.1', port=8000, debug=False)

__all__ = ['run_internal_service']
