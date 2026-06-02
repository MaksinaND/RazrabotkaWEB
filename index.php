<?php
/* ============================================================
   Лабораторная работа № В-2
   Тема: преобразование типов и сессии.
   Задача: калькулятор с историей вычислений.

   Важно: session_start() стоит самым первым, до любого HTML,
   потому что сессии работают через HTTP-заголовки.
   ============================================================ */

session_start();

/* ---------- Подготовка данных сессии ---------- */

// История хранит только прошлые вычисления.
// Текущий результат в историю сразу не добавляется.
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = array();
}

// current_entry — последнее вычисление, которое сейчас показывается сверху.
// Оно попадет в историю только перед следующим НОВЫМ вычислением.
if (!isset($_SESSION['current_entry'])) {
    $_SESSION['current_entry'] = '';
}

// Токен нужен, чтобы при обновлении страницы F5 форма не добавляла одно и то же вычисление повторно.
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = md5(uniqid('', true));
}

$currentExpression = '';
$currentResult = '';
$isError = false;
$showResult = false;

/* ---------- Вспомогательные функции для разбора выражения ---------- */

// Проверяем, является ли символ цифрой.
function isDigitChar($ch) {
    return $ch >= '0' && $ch <= '9';
}

// Удаляем пробелы из выражения, чтобы "2 + 3" стало "2+3".
function removeSpaces($str) {
    $result = '';

    for ($i = 0; $i < strlen($str); $i++) {
        if ($str[$i] !== ' ' && $str[$i] !== "\t" && $str[$i] !== "\n" && $str[$i] !== "\r") {
            $result .= $str[$i];
        }
    }

    return $result;
}

// Переводим строковое число в настоящее число вручную.
// Это сделано без eval(), чтобы соответствовать смыслу лабораторной.
function stringToNumber($str) {
    $number = 0;
    $afterPoint = false;
    $divider = 10;

    for ($i = 0; $i < strlen($str); $i++) {
        $ch = $str[$i];

        if ($ch === '.' || $ch === ',') {
            $afterPoint = true;
            continue;
        }

        $digit = ord($ch) - ord('0');

        if (!$afterPoint) {
            $number = $number * 10 + $digit;
        } else {
            $number = $number + $digit / $divider;
            $divider = $divider * 10;
        }
    }

    return $number;
}

// Форматируем результат: если получилось 10.000000, выводим просто 10.
function formatResult($num) {
    if (abs($num - round($num)) < 0.0000000001) {
        return (string)round($num);
    }

    $txt = number_format($num, 10, '.', '');

    while (strlen($txt) > 0 && $txt[strlen($txt) - 1] === '0') {
        $txt = substr($txt, 0, strlen($txt) - 1);
    }

    if (strlen($txt) > 0 && $txt[strlen($txt) - 1] === '.') {
        $txt = substr($txt, 0, strlen($txt) - 1);
    }

    return $txt;
}

/* ---------- Рекурсивный разбор выражения ----------
   parseExpression() обрабатывает + и -
   parseTerm()       обрабатывает * и /
   parseFactor()     обрабатывает числа, скобки и унарный минус
   Такой порядок нужен из-за правил математики:
   сначала скобки, затем умножение/деление, затем сложение/вычитание.
   -------------------------------------------------- */

function parseExpression($str, &$pos, &$error) {
    $value = parseTerm($str, $pos, $error);

    while ($error === '' && $pos < strlen($str)) {
        $op = $str[$pos];

        if ($op !== '+' && $op !== '-') {
            break;
        }

        $pos++;
        $right = parseTerm($str, $pos, $error);

        if ($error !== '') {
            return 0;
        }

        if ($op === '+') {
            $value += $right;
        } else {
            $value -= $right;
        }
    }

    return $value;
}

function parseTerm($str, &$pos, &$error) {
    $value = parseFactor($str, $pos, $error);

    while ($error === '' && $pos < strlen($str)) {
        $op = $str[$pos];

        if ($op !== '*' && $op !== '/' && $op !== ':') {
            break;
        }

        $pos++;
        $right = parseFactor($str, $pos, $error);

        if ($error !== '') {
            return 0;
        }

        if ($op === '*') {
            $value *= $right;
        } else {
            if (abs($right) < 0.0000000001) {
                $error = 'деление на ноль';
                return 0;
            }
            $value /= $right;
        }
    }

    return $value;
}

function parseFactor($str, &$pos, &$error) {
    if ($pos >= strlen($str)) {
        $error = 'выражение закончилось неожиданно';
        return 0;
    }

    // Унарный плюс или минус, например: -5 или 2*(-3).
    if ($str[$pos] === '+') {
        $pos++;
        return parseFactor($str, $pos, $error);
    }

    if ($str[$pos] === '-') {
        $pos++;
        return -parseFactor($str, $pos, $error);
    }

    // Если встретилась открывающая скобка, считаем выражение внутри нее.
    if ($str[$pos] === '(') {
        $pos++;
        $value = parseExpression($str, $pos, $error);

        if ($error !== '') {
            return 0;
        }

        if ($pos >= strlen($str) || $str[$pos] !== ')') {
            $error = 'неправильная расстановка скобок';
            return 0;
        }

        $pos++;
        return $value;
    }

    // Если это не скобка, значит ожидаем число.
    return parseNumber($str, $pos, $error);
}

function parseNumber($str, &$pos, &$error) {
    $num = '';
    $pointCount = 0;

    while ($pos < strlen($str)) {
        $ch = $str[$pos];

        if (isDigitChar($ch)) {
            $num .= $ch;
            $pos++;
        } elseif ($ch === '.' || $ch === ',') {
            $pointCount++;

            if ($pointCount > 1) {
                $error = 'неправильная форма числа';
                return 0;
            }

            $num .= '.';
            $pos++;
        } else {
            break;
        }
    }

    if ($num === '' || $num === '.') {
        $error = 'ожидалось число';
        return 0;
    }

    if ($num[0] === '.' || $num[strlen($num) - 1] === '.') {
        $error = 'неправильная форма числа';
        return 0;
    }

    return stringToNumber($num);
}

// Главная функция вычисления выражения.
function calculate($expr) {
    $expr = removeSpaces($expr);

    if ($expr === '') {
        return array(false, 'выражение не задано');
    }

    $pos = 0;
    $error = '';
    $value = parseExpression($expr, $pos, $error);

    if ($error !== '') {
        return array(false, $error);
    }

    if ($pos < strlen($expr)) {
        return array(false, 'недопустимый символ: ' . $expr[$pos]);
    }

    return array(true, formatResult($value));
}

/* ---------- Обработка формы ---------- */

if (isset($_POST['val'])) {
    $currentExpression = $_POST['val'];

    // Если токен совпал, значит это новая отправка формы, а не повторное F5.
    if (isset($_POST['form_token']) && $_POST['form_token'] === $_SESSION['form_token']) {
        // Предыдущий показанный сверху результат переносим в историю один раз.
        if ($_SESSION['current_entry'] !== '') {
            $_SESSION['history'][] = $_SESSION['current_entry'];
        }

        $calc = calculate($currentExpression);
        $isOk = $calc[0];
        $answer = $calc[1];

        if ($isOk) {
            $currentResult = 'Значение выражения: ' . $answer;
            $isError = false;
            $_SESSION['current_entry'] = $currentExpression . ' = ' . $answer;
        } else {
            $currentResult = 'Ошибка вычисления выражения: ' . $answer;
            $isError = true;
            $_SESSION['current_entry'] = $currentExpression . ' = ошибка: ' . $answer;
        }

        $showResult = true;

        // Меняем токен, чтобы повторное обновление страницы не добавляло дубль.
        $_SESSION['form_token'] = md5(uniqid('', true));
    } else {
        // Если это повторная отправка старой формы, просто показываем текущий результат.
        if ($_SESSION['current_entry'] !== '') {
            $currentResult = $_SESSION['current_entry'];
            $showResult = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лабораторная работа №10</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 950px;
            margin: 0 auto;
            background: #fff;
            padding: 35px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        h1 {
            margin-top: 0;
            font-size: 42px;
        }

        label {
            display: block;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        input[type="text"] {
            width: 100%;
            padding: 14px;
            font-size: 22px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            margin-top: 18px;
            padding: 12px 24px;
            font-size: 20px;
            background: #1f67ad;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .hint {
            margin-top: 18px;
            color: #555;
            font-size: 18px;
        }

        .result {
            padding: 18px;
            margin: 22px 0;
            font-size: 24px;
            border-radius: 5px;
        }

        .ok {
            color: green;
            background: #eaf7ec;
            border: 1px solid #8bd29a;
        }

        .bad {
            color: #b00020;
            background: #ffecec;
            border: 1px solid #ffaaaa;
        }

        hr {
            margin: 35px 0;
        }

        .history-line {
            padding: 9px 0;
            border-bottom: 1px dashed #ddd;
            font-size: 18px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Калькулятор</h1>

    <?php if ($showResult): ?>
        <div class="result <?php echo $isError ? 'bad' : 'ok'; ?>">
            <?php echo htmlspecialchars($currentResult); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <label for="val">Введите выражение:</label>
        <input
            type="text"
            name="val"
            id="val"
            value="<?php echo htmlspecialchars($currentExpression); ?>"
            placeholder="Например: (2+3)*4/2"
        >
        <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">
        <br>
        <input type="submit" value="Вычислить">
    </form>

    <div class="hint">
        Можно использовать целые числа, десятичные дроби, операции +, -, *, /, : и скобки.
    </div>

    <hr>

    <h2>История вычислений</h2>

    <?php if (count($_SESSION['history']) === 0): ?>
        <p>История пока пустая.</p>
    <?php else: ?>
        <?php for ($i = 0; $i < count($_SESSION['history']); $i++): ?>
            <div class="history-line">
                <?php echo htmlspecialchars($_SESSION['history'][$i]); ?>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
</div>
</body>
</html>
