from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/scrape', methods=['POST'])
def scrape():
    data = request.json
    query = data.get('query')
    location = data.get('location')
    # Aqui você implementará a lógica real de scraping
    # Por enquanto, retorna dados simulados
    results = [
        {'title': f'Vaga {query}', 'company': 'Empresa Exemplo', 'location': location}
    ]
    return jsonify(results)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)