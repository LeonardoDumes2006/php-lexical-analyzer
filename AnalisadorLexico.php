<?php

class AnalisadorLexico {
    private string $codigo;
    private int $ponteiro = 0;
    private int $tamanho;
    
    private array $palavrasReservadas = [
        'if', 'else', 'while', 'for', 'class', 'function', 'return', 
        'public', 'private', 'echo', 'print', 'new', 'null', 'true', 'false'
    ];

    public function __construct(string $codigo) {
        $this->codigo = $codigo;
        $this->tamanho = strlen($codigo);
    }

    public function analisar(): array {
        $tokens = [];

        while ($this->ponteiro < $this->tamanho) {
            $char = $this->codigo[$this->ponteiro];

            if (ctype_space($char)) {
                $this->ponteiro++;
                continue;
            }

            if ($char === '$') {
                $tokens[] = $this->lerVariavelPhp();
                continue;
            }

            if (ctype_alpha($char) || $char === '_') {
                $tokens[] = $this->lerIdentificador();
                continue;
            }

            if (ctype_digit($char)) {
                $tokens[] = $this->lerNumero();
                continue;
            }

            if ($char === '"' || $char === "'") {
                $tokens[] = $this->lerLiteral($char);
                continue;
            }

            $operadores = ['+', '-', '/', '*', '^', '<', '>', '=', '!', '~'];
            if (in_array($char, $operadores)) {
                $tokens[] = $this->lerOperador();
                continue;
            }

            $delimitadores = ['(', ')', '{', '}', '[', ']', ';', ',', '.'];
            if (in_array($char, $delimitadores)) {
                $tokens[] = ['tipo' => 'Delimitador', 'valor' => $char];
                $this->ponteiro++;
                continue;
            }

            $tokens[] = ['tipo' => 'Erro Léxico', 'valor' => $char];
            $this->ponteiro++;
        }

        return $tokens;
    }

   private function lerVariavelPhp(): array {
        $lexema = '$';
        $this->ponteiro++; // Avança o cifrão

        // Verifica se chegamos ao fim do arquivo logo após o '$'
        if ($this->ponteiro >= $this->tamanho) {
            return ['tipo' => 'Erro Léxico (Variável Incompleta)', 'valor' => $lexema];
        }

        $primeiroChar = $this->codigo[$this->ponteiro];

        // REGRA 1: O primeiro caractere após o '$' DEVE ser uma letra ou '_'
        if (ctype_alpha($primeiroChar) || $primeiroChar === '_') {
            $lexema .= $primeiroChar;
            $this->ponteiro++;
        } else {
            // Se for um número ou símbolo inválido, capturamos como erro
            $lexema .= $primeiroChar;
            $this->ponteiro++;
            return ['tipo' => 'Erro Léxico (Nome de Variável Inválido)', 'valor' => $lexema];
        }

        // REGRA 2: Os próximos caracteres podem ser letras, números ou '_'
        while ($this->ponteiro < $this->tamanho) {
            $charAtual = $this->codigo[$this->ponteiro];
            
            if (ctype_alnum($charAtual) || $charAtual === '_') {
                $lexema .= $charAtual;
                $this->ponteiro++;
            } else {
                break;
            }
        }

        return ['tipo' => 'Variável PHP', 'valor' => $lexema];
    }

    private function lerIdentificador(): array {
        $lexema = '';

        while ($this->ponteiro < $this->tamanho && (ctype_alnum($this->codigo[$this->ponteiro]) || $this->codigo[$this->ponteiro] === '_')) {
            $lexema .= $this->codigo[$this->ponteiro];
            $this->ponteiro++;
        }

        $tipo = in_array(strtolower($lexema), $this->palavrasReservadas) ? 'Palavra Reservada' : 'Identificador';
        return ['tipo' => $tipo, 'valor' => $lexema];
    }

    private function lerNumero(): array {
        $lexema = '';
        $ehReal = false;

        while ($this->ponteiro < $this->tamanho && (ctype_digit($this->codigo[$this->ponteiro]) || $this->codigo[$this->ponteiro] === '.')) {
            if ($this->codigo[$this->ponteiro] === '.') {
                if ($ehReal) break; 
                $ehReal = true;
            }
            $lexema .= $this->codigo[$this->ponteiro];
            $this->ponteiro++;
        }

        return ['tipo' => $ehReal ? 'Número Real' : 'Número Inteiro', 'valor' => $lexema];
    }

    private function lerLiteral(string $tipoAspa): array {
        $lexema = $tipoAspa;
        $this->ponteiro++; 

        while ($this->ponteiro < $this->tamanho && $this->codigo[$this->ponteiro] !== $tipoAspa) {
            $lexema .= $this->codigo[$this->ponteiro];
            $this->ponteiro++;
        }

        if ($this->ponteiro < $this->tamanho) {
            $lexema .= $this->codigo[$this->ponteiro]; 
            $this->ponteiro++;
        }

        return ['tipo' => 'Literal', 'valor' => $lexema];
    }

    private function lerOperador(): array {
        $char = $this->codigo[$this->ponteiro];
        $proxChar = ($this->ponteiro + 1 < $this->tamanho) ? $this->codigo[$this->ponteiro + 1] : '';
        
        if ($char === '!' && $proxChar === '=') {
            $this->ponteiro += 2;
            return ['tipo' => 'Operador Relacional', 'valor' => '!='];
        }

        $this->ponteiro++;
        
        $tipo = 'Operador Matemático';
        if (in_array($char, ['<', '>', '='])) $tipo = 'Operador Relacional';
        if ($char === '~') $tipo = 'Operador Lógico';

        return ['tipo' => $tipo, 'valor' => $char];
    }
}