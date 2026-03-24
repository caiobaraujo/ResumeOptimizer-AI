from flask import Flask, request, jsonify
import requests
from bs4 import BeautifulSoup
import re

app = Flask(__name__)

@app.route('/scrape', methods=['POST'])
def scrape():
    data = request.json
    url = data.get('url')

    if not url:
        return jsonify({'error': 'URL não fornecida', 'success': False}), 400

    try:
        # Headers falsos para simular um navegador real. 
        # Isso evita que sites como LinkedIn bloqueiem nosso robô imediatamente.
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
            'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7'
        }
        
        # Fazemos o download da página (timeout de 10 segundos para não travar o sistema)
        response = requests.get(url, headers=headers, timeout=10)
        response.raise_for_status() # Se der erro 404 ou 500, cai no except abaixo

        # O BeautifulSoup transforma o HTML bagunçado em uma árvore navegável
        soup = BeautifulSoup(response.text, 'html.parser')

        # Removemos lixos que não interessam para a IA ler (Scripts, Estilos, Menus, Rodapés)
        for script in soup(["script", "style", "nav", "footer", "header", "aside"]):
            script.extract()

        # Extraímos apenas o texto visível da página
        text = soup.get_text(separator=' ')
        
        # Limpamos espaços duplos e quebras de linha em excesso (Regex)
        clean_text = re.sub(r'\s+', ' ', text).strip()

        # Retornamos o texto puro para o Laravel
        return jsonify({'text': clean_text, 'success': True})

    except requests.exceptions.RequestException as e:
        # Log do erro para monitorarmos por que o scraping falhou (Requisito do seu projeto)
        print(f"Erro de conexão ao acessar {url}: {str(e)}")
        return jsonify({'error': 'Falha ao acessar o site. O site pode estar bloqueando robôs.', 'success': False}), 500
    except Exception as e:
        print(f"Erro desconhecido: {str(e)}")
        return jsonify({'error': 'Erro interno no robô de extração.', 'success': False}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001)