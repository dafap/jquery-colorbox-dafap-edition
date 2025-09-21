<?php
/**
 * jQuery Colorbox Dafap Edition
 *
 * @package   jquery-colorbox-dafap-edition
 * @author    Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken
 * @copyright 2025 Alain Pomirol
 * @license   GPL-2.0-or-later
 * @link      https://github.com/dafap/jquery-colorbox-dafap-edition
 * @version   5.0
 * @since     5.0
 * @file      class-jquery-colorbox-debugger.php
 * @purpose   Classe utilitaire pour afficher les variables dans wp_die() avec mise en forme HTML
 */

class JQueryColorboxDebugger {

  /**
   * Affiche toutes les variables définies dans le contexte actuel via wp_die().
   *
   * @return void
   */
  public function dieWithAllVariables(): void {
    wp_die(var_dump(get_defined_vars()));
  }

  /**
   * Affiche une variable spécifique via wp_die(), avec mise en forme HTML.
   *
   * @param mixed  $variable La variable à afficher
   * @param string|null $title Titre optionnel pour la sortie
   * @return void
   */
  public function dieWithVariable(mixed $variable, ?string $title = null): void {
    wp_die($this->dumpVariable($variable), $title);
  }

  /**
   * Prépare l'affichage HTML d'une variable.
   *
   * @param mixed $var La variable à afficher
   * @param bool  $info Affiche un titre si TRUE
   * @return string HTML formaté
   */
  private function dumpVariable(mixed &$var, bool $info = false): string {
    $scope = false;
    $prefix = 'unique';
    $suffix = 'value';

    $vals = $scope ?: $GLOBALS;

    $old = $var;
    $var = $new = $prefix . rand() . $suffix;
    $vname = false;
    foreach ($vals as $key => $val) {
      if ($val === $new) {
        $vname = $key;
        break;
      }
    }
    $var = $old;

    ob_start();
    echo "<pre style='margin: 0 0 10px 0; display: block; background: white; color: black; font-family: Verdana; border: 1px solid #cccccc; padding: 5px; font-size: 10px; line-height: 13px;'>";
    if ($info) {
      echo "<b style='color: red;'>$info:</b><br>";
    }
    $this->doDumpVariable($var, '$' . $vname);
    echo "</pre>";
    return ob_get_clean();
  }

  /**
   * Affiche récursivement une variable avec indentation et typage.
   *
   * @param mixed       $var        La variable à afficher
   * @param string|null $var_name   Nom de la variable
   * @param string|null $indent     Indentation HTML
   * @param string|null $reference  Référence pour éviter les boucles
   * @return void
   */
  private function doDumpVariable(mixed &$var, ?string $var_name = null, ?string $indent = null, ?string $reference = null): void {
    $do_dump_indent = "<span style='color:#eeeeee;'>|</span> &nbsp;&nbsp; ";
    $reference .= $var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme';
    $keyname = 'referenced_object_name';

    if (is_array($var) && isset($var[$keyvar])) {
      $real_var = &$var[$keyvar];
      $real_name = &$var[$keyname];
      $type = ucfirst(gettype($real_var));
      echo "$indent$var_name <span style='color:#a2a2a2'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
      return;
    }

    $var = [$keyvar => $var, $keyname => $reference];
    $avar = &$var[$keyvar];
    $type = ucfirst(gettype($avar));
    $type_color = match ($type) {
      'String' => "<span style='color:green'>",
      'Integer' => "<span style='color:red'>",
      'Float', 'Double' => "<span style='color:#0099c5'>",
      'Boolean' => "<span style='color:#92008d'>",
      'NULL' => "<span style='color:black'>",
      default => "<span>",
    };

    if (is_array($avar)) {
      $count = count($avar);
      echo "$indent" . ($var_name ? "$var_name => " : "") . "<span style='color:#a2a2a2'>$type ($count)</span><br>$indent(<br>";
      foreach ($avar as $name => &$value) {
        $this->doDumpVariable($value, "['$name']", $indent . $do_dump_indent, $reference);
      }
      echo "$indent)<br>";
    } elseif (is_object($avar)) {
      echo "$indent$var_name <span style='color:#a2a2a2'>$type</span><br>$indent(<br>";
      foreach (get_object_vars($avar) as $name => &$value) {
        $this->doDumpVariable($value, "$name", $indent . $do_dump_indent, $reference);
      }
      echo "$indent)<br>";
    } elseif (is_int($avar) || is_float($avar)) {
      echo "$indent$var_name = <span style='color:#a2a2a2'>$type</span> $type_color$avar</span><br>";
    } elseif (is_string($avar)) {
      echo "$indent$var_name = <span style='color:#a2a2a2'>$type(" . strlen($avar) . ")</span> $type_color\"$avar\"</span><br>";
    } elseif (is_bool($avar)) {
      echo "$indent$var_name = <span style='color:#a2a2a2'>$type</span> $type_color" . ($avar ? "TRUE" : "FALSE") . "</span><br>";
    } elseif (is_null($avar)) {
      echo "$indent$var_name = <span style='color:#a2a2a2'>$type</span> {$type_color}NULL</span><br>";
    } else {
      echo "$indent$var_name = <span style='color:#a2a2a2'>$type</span> $avar<br>";
    }

    $var = $var[$keyvar];
  }

}
