<div align="justify">

# Sistema Sem Fio Para Detecçao de Incêndio e Alagamentos em Embarcações
Trabalho de Conclusão de Curso em Desenvolvimento para Curso Superior de Tecnologia em Redes de Computadores - IFRN 2026
<img width="1280" height="720" alt="Imagens do TCC" src="https://github.com/user-attachments/assets/c670eca9-6129-4c8f-bb2c-273c1274db83" />
<details>
<summary>RESUMO</summary>
Neste projeto, descreve-se o desenvolvimento e a implementação de um sistema sem fio  voltado ao monitoramento, detecção e emissão de alertas para incêndios e alagamentos em embarcações.  O sistema foi concebido a partir da utilização de plataformas microcontroladas do tipo ESP8266 integradas a sensores de temperatura, umidade, gases e nível de água. O monitoramento é realizado por meio de plataforma própria e por dashboads do Grafana.
</details>

<details>
<summary>INTRODUÇÃO</summary>
Detectar rapidamente incêndios e alagamentos em embarcações é fundamental para permitir uma resposta eficaz a esses acontecimentos, pois atrasos nessa identificação podem causar danos irreversíveis aos equipamentos, à tripulação e ao meio ambiente [1]. Incidentes como este, quando acontecem em ambientes marítimos, costumam trazer grandes problemas devido ao fato de todos estarem isolados em alto mar e sem acesso a serviços essenciais como por exemplo, socorro médico e bombeiros. 

Quando a fumaça ou a água se espalham pelos corredores, fica difícil definir com precisão onde está o foco do problema, retardando assim o início da ação de combate. Em instituições doutrinárias como a Marinha do Brasil, considera-se que o ideal é descobrir a ocorrência em até três minutos após o seu início sob possibilidade de tornar-se um evento fora de controle [2]. 

Uma maneira de acelerar essa detecção seria através de sistemas automatizados que monitoram e avisam sobre esses eventos. No entanto, tais sistemas geralmente apresentam alto custo e complexidade de implementação quando levamos em consideração a ampla variedade dos meios navais existentes na atualidade. 

Este trabalho propõe uma alternativa de baixo custo e grande potencial baseada no uso de dispositivos Internet of Things (IoT) em uma rede sem fio e Grafana. Considerando critérios como custo, facilidade de instalação e funcionalidade, descreve-se como desenvolver um sistema utilizando plataformas microcontroladas e diferentes sensores, capazes de monitorar e identificar avarias a bordo de embarcações de forma confiável.
</details>

<details>
<summary>OBJETIVOS</summary>
  
### Objetivos Gerais
  
Este trabalho tem como objetivo desenvolver um sistema de baixo custo em rede de computadores voltado à segurança da navegação aquaviária, capaz de realizar o monitoramento contínuo de variáveis ambientais, detectar situações anômalas em tempo real e emitir alertas adequados, contribuindo para a tomada rápida de decisões e para a prevenção de acidentes durante a navegação.

### Objetivos Específicos

a) Desenvolver protótipos de sensores capazes de monitorar variáveis ambientais e enviar dados por meio de rede sem fio;<br>
b) Configurar ambiente com servidor web, banco de dados e grafana;<br>
c) Desenvolver interface gráfica para acompanhamento por meio de dashboards; e<br>
d) Desenvolver interface gráfica para monitoramento de alarmes.
</details>

<details>
<summary>METODOLOGIA</summary>
Esta seção descreve a estrutura do sistema proposto, bem como os componentes utilizados e a forma como ocorre a coleta, a transmissão e o processamento dos dados. O sistema foi idealizado com o objetivo de oferecer uma solução de baixo custo para monitoramento e alerta de incêndios e alagamentos em embarcações, priorizando simplicidade de implementação e eficiência na detecção.
  
### Visão geral do sistema
O sistema desenvolvido é estruturado em três processos principais: sensoriamento, comunicação e processamento. Esses processos são integrados de forma a viabilizar o monitoramento contínuo e em tempo real das condições ambientais da embarcação. 

O processo de sensoriamento é composto por sensores e microcontroladores responsáveis pela aquisição periódica de variáveis como temperatura, concentração de gases e presença de água. Esses dispositivos realizam o pré-processamento dos dados e efetuam sua transmissão ao servidor por meio de uma rede sem fio e protocolo HTTP para processamento.

Durante o processamento, os dados coletados são recebidos por um servidor, onde são tratados e armazenados em um banco de dados. Essa abordagem permite a organização estruturada das informações, como a identificação dos sensores e variáveis monitoradas. Como resultado, torna-se possível a realização de análises históricas, correlação entre variáveis e identificação de padrões que possam indicar situações de risco ou comportamento anômalo do ambiente monitorado.

Os dados armazenados também são integrados à plataforma de visualização Grafana, que possibilita a construção de dashboards interativos e personalizáveis. Por meio desses painéis, os usuários podem acompanhar em tempo real as condições monitoradas, além de consultar o histórico de medições de forma intuitiva. Além da visualização proporcionada pelo Grafana, foi elaborado uma interface própria para configuração e visualização de alarmes de forma intuitiva.
<div align="center">
  <img src="https://github.com/user-attachments/assets/0e04abd8-e761-4db8-9313-1bac6a155dc1"" width="300px" />
  <p><em>Diagrama simplificado do sistema</em></p>
</div>


### Componentes de hardware
A arquitetura de hardware foi definida considerando disponibilidade, custo e compatibilidade entre os dispositivos. O sistema utiliza microcontroladores baseados no módulo ESP8266 Wemos Mini D1, escolhido devido à sua capacidade de processamento e conectividade Wi-Fi integrada. Este módulo possui 11 pinos de entrada/saída digital e 01 analógica, trabalha com com alimentação de 3,3V e suporta protocolos UART, I2C e SPI.

Para o monitoramento de gases, foi empregado um sensor do tipo MQ-7, adequado para detecção de monóxido de carbono (CO). Este sensor possui uma faixa de detecção de 10 a 10.000 ppm (partes por milhão), possui saída análogica e digital e trabalha com tensão de alimentação entre 3V e 5V DC.

A medição de temperatura é realizada por meio do sensor AHT10, que apresenta boa precisão e estabilidade para aplicações embarcadas. Este sensor possui uma faixa de medição de temperatura de -40°C a +80°C com uma precisão de ±0,3°C, trabalha com protocolo I2C e alimentação entre 1,8V e 3,6V.

Por fim, para monitoramento de alagamento foi utilizado o sensor de nível de água HW-028 que possui saída digital que indica presença ou ausência de água e trabalha com tensão de 3,3V a 5V. 

Também foi acrescentado um módulo relé com microcontrolador e conectividade Wi-Fi integrada para acionamento de dispositivos externos, como alarmes sonoros, permitindo uma resposta imediata em situações críticas.
<div align="center">
  <table>
    <thead>
      <tr>
        <th>Item</th>
        <th>Descrição</th>
        <th>Preço médio (R$)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>Placa Arduíno WeMos D1 Mini</td>
        <td>30,00</td>
      </tr>
      <tr>
        <td>2</td>
        <td>Sensor de temperatura e umidade AHT-10</td>
        <td>15,00</td>
      </tr>
      <tr>
        <td>3</td>
        <td>Sensor de Monóxido de Carbono MQ-7</td>
        <td>20,00</td>
      </tr>
      <tr>
        <td>4</td>
        <td>Sensor de nível de água HW-028</td>
        <td>20,00</td>
      </tr>
      <tr>
        <td>5</td>
        <td>Fonte de alimentação 5V</td>
        <td>15,00</td>
      </tr>
    </tbody>
  </table>
  <p align="center">
    <em>Preço médio dos dispositivos utilizados no sistema.</em>
  </p>
</div>
Além dos componentes apresentados, é necessário um servidor, que poderá ser um computador com sistema operacional Linux conectado a uma rede wireless e um alarme que possa ser acionado por relé. A figura a seguir demonstra a arquitetura de hardware proposta.
<div align="center">
  <img src="https://github.com/user-attachments/assets/f3c701cb-3809-4b54-8a86-dddc79d24f4d" width="300px" />
  <p><em>Arquitetura de hardware</em></p>
</div>

### Coleta e envio de dados
A obtenção dos dados de temperatura, gases e existência de água decorrente de alagamentos se dá por meio de unidades compostas por sensores conectados a microcontroladores. O sistema foi configurado de modo a executar leituras e transmissões em intervalos de 5 segundos, visando sustentar uma frequência elevada de amostragem, o que por sua vez coopera para que haja uma resposta mais ágil no enfrentamento de situações críticas.

Os dados que são coletados seguem para o servidor através do protocolo HTTP, fazendo uso de uma rede sem fio. A fim de mitigar eventuais ocorrências de interferências dentro do sistema, foi implementado um pequeno atraso aleatório antes do envio das informações, reduzindo assim a probabilidade de colisões entre os dispositivos que dividem o mesmo canal de comunicação. Além disso, cada unidade conta com um identificador exclusivo, o qual é inserido em cada pacote que se envia, possibilitando ao servidor que reconheça a procedência dos dados e realize o seu armazenamento de maneira adequada junto ao banco de dados.


### Processamento dos dados
Os dados obtidos pelos sensores são encaminhados ao servidor e armazenados em um banco de dados. Nesse ambiente, os dados são recepcionados por um script que verifica a origem, filtra as informações e inseri-as no banco de dados.

Uma vez armazenados, esses dados passam a ser acessados pela plataforma Grafana, utilizada para fins de visualização e análise dos dados. Por meio dessa ferramenta, são elaborados painéis que permitem ao usuário acompanhar o comportamento das variáveis monitoradas.

Além da função de visualização, o Grafana também é empregado na definição de regras de alerta. O usuário pode estabelecer limites para cada variável e, sempre que esses valores são excedidos é disparado um script que aciona um alarme externo.

Por fim, destaca-se que essa organização separa a etapa de coleta de dados das camadas de visualização e gerenciamento de alertas. Essa separação contribui para tornar a solução mais flexível, facilitando ajustes e adaptações conforme as necessidades de diferentes aplicações.
</details>
<details>
<summary>IMPLEMENTAÇÃO</summary>
Nesta seção, serão apresentados os detalhes da implementação de cada nó, do servidor e do processo de integração com o Grafana.

### Ambiente de desenvolvimento


### Implementação do nó sensor
Cada nó sensor é responsável por coletar os dados dos sensores conectados ao seu microcontrolador, bem como por encaminhar informações provenientes de outros nós quando necessário. Para que este processo aconteça

### Comunicação e rede

### Servidor e banco de dados

### Integração com visualização e alertas

### Prototipagem e Testes

</details>
</div>
