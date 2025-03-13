<?php

declare(strict_types=1);

namespace Plateformweb\Datatables;

class CustomButton extends Debug
{
    private string $text;
    private array $columns = [];
    private array $extraColumns = [];
    private array $style = [];
    private array $class = [];
    private bool $enabled = false;
    private bool $ajax = false;
    private ?string $url = null;
    private ?string $modalId = null;
    private ?string $response = null;
    private ?string $confirmText = null;
    private bool $confirm = false;

    public function __construct(string $text)
    {
        $this->text = htmlentities($text);
    }

    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Permet d'ajouter et de poster des colonnes virtuel non présentes dans le datatable
     * comme par exemple la key => value d'un $_GET
     * ['annee' => 2024, ...]
     */
    public function setExtraColumns(array $extraColumns): self
    {
        $this->extraColumns = array_merge($this->extraColumns, $extraColumns);

        return $this;
    }

    public function setStyle(array $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function setClass(array $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        if ($enabled === true) {
            $this->extraColumns = $this->columns = [];
        }

        return $this;
    }

    public function setAjax(bool $ajax = true): self
    {
        $this->ajax = $ajax;

        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Ideal pour la popup
     * 
     * @param string $modalId L'ID du modal où le contenu sera injecté
     */
    public function setModal(string $modalId): self
    {
        $this->modalId = $modalId;

        return $this;
    }

    public function getModal(): ?string
    {
        return $this->modalId;
    }

    public function setResponse(string $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function setConfirm(string $confirmText, bool $confirm = true): self
    {
        $this->confirmText = htmlentities($confirmText);
        $this->confirm = $confirm;

        return $this;
    }

    private function encodeJson(array $datas): string
    {
        return json_encode($datas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function convertBooleanToJs(bool $value): string
    {
        return $value ? 'true' : 'false';
    }

    private function generateSimpleButtonAction(): string
    {
        if ($this->url) {
            return <<<JS
                window.location = '{$this->url}';
            JS;
        }

        return '';
    }

    private function generateButtonAction(): string
    {
        // Génére le code JavaScript pour l'action du button
        return <<<JS
            var rows = dt.rows({ selected: true }).count();
            var datas = dt.rows({ selected: true }).data();

            // Récupérer les indices des lignes sélectionnées
            var indexes = {rows: dt.rows({ selected: true }).indexes().toArray()};

            var columns = JSON.parse('{$this->encodeJson($this->columns)}');
            var extraColumns = JSON.parse('{$this->encodeJson($this->extraColumns)}');

            for (var s = 0; s < rows; s++) {
                var rowData = datas[s];
                Object.keys(columns).forEach(function (index) {
                    columns[index].datas.push(rowData[columns[index].key]);
                });
            }

            var columns = Object.values(columns).reduce((result, column) => {
                result[column.key] = column.datas;
                return result;
            }, {});

            var ajaxModal = (datas) => {
                $.ajax({
                    url: '{$this->url}',
                    method: 'POST',
                    data: datas,
                    dataType: 'html',
                    beforeSend: function() {
                        $('#{$this->modalId} .modal-body').html('');
                        $('#{$this->modalId}').modal('hide');
                    },
                    success: function(reponse) {
                        $('#{$this->modalId} .modal-body').html(reponse);
                        $('#{$this->modalId}').modal({
                            escapeClose: false,
                            clickClose: false,
                            showClose: true
                        });
                    }
                });
            }

            var ajax = (datas) => {
                $.ajax({
                    url: '{$this->url}',
                    method: 'POST',
                    data: datas,
                    dataType: 'html',
                    success: function(response) {
                        if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                            console.log('Response: ', response);
                        }
                        dt.rows().deselect();
                        {$this->response}
                    }
                });
            }

            if ('{$this->convertBooleanToJs($this->confirm)}' === 'true') {
                $.confirm({
                    title: 'Confirmation',
                    content: '{$this->confirmText}',
                    buttons: {
                        confirm: {
                            text: t_confirm,
                            action: function () {
                                ajax({...columns, ...extraColumns, ...indexes});
                            }
                        },
                        cancel: {
                            text: t_cancel,
                            action: function () {
                                if ('{$this->convertBooleanToJs($this->debug)}' === 'true') {
                                    console.log('rows', {...columns, ...extraColumns, ...indexes})
                                }
                            }
                        }
                    }
                });
            } else {
                if ('{$this->modalId}' == '') {
                    ajax({...columns, ...extraColumns, ...indexes});
                } else {
                    ajaxModal({...columns, ...extraColumns, ...indexes});
                }
            }
        JS;
    }

    public function generate(): array
    {
        return array_merge(
            [
                'text' => $this->text,
                'action' => $this->ajax === false ? $this->generateSimpleButtonAction() : $this->generateButtonAction(), 
                'enabled' => $this->enabled, 
            ], 
            (!empty($this->style) ? ['attr' => ['style' => implode(' ', $this->style)]] : []), 
            (!empty($this->class) ? ['className' => implode(' ', $this->class)] : [])
        );
    }
}
